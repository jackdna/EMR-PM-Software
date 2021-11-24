<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$tablename = "preopphysicianorders";
//include("common/linkfile.php");
include("common_functions.php");
include_once("admin/classObjectFunction.php"); 
$objManageData = new manageData;
include("new_header_print.php");
extract($_GET);
$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];

$patient_id = $_REQUEST['patient_id'];
if(!$patient_id) {
	$patient_id = $_SESSION['patient_id'];
}	

$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {
	$pConfId = $_SESSION['pConfId'];
}	
$cancelRecord = $_REQUEST['cancelRecord'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];
$relivednurse = $_REQUEST['relived_nurse'];
$Heparin_time=$_REQUEST['Heparin_time'];
$Heparin_start_user=$_REQUEST['Heparin_start_user'];


//UPDATING PATIENT STATUS IN STUB TABLE

 $patientdata=imw_query("select patientconfirmation.patientId,patientconfirmation.dos,
patientconfirmation.surgery_time,patient_data_tbl.patient_fname,patient_mname,
patient_data_tbl.patient_lname,patient_data_tbl.date_of_birth from patientconfirmation 
left join patient_data_tbl on patientconfirmation.ascId = patient_data_tbl.asc_id
where  patientconfirmation.patientConfirmationId='$pConfId'");
while($patient_data=imw_fetch_array($patientdata))
{
   $dos= $patient_data['dos'];
   $surgerytime=$patient_data['surgery_time'];
   $patient_fname=$patient_data['patient_fname'];
   $patient_mname=$patient_data['patient_mname'];
   $patient_lname=$patient_data['patient_lname'];
   $dob=$patient_data['date_of_birth'];
}

 $stub_data=imw_query("select * from stub_tbl where dos='$dos' && patient_first_name ='$patient_fname' && patient_middle_name='$patient_mname'
&& patient_last_name='$patient_lname' && patient_dob='$dob' && surgery_time='$surgerytime'");

while($stubtbl_data=imw_fetch_array($stub_data))
{
  $stub_id=$stubtbl_data['stub_id'];
} 
if($_REQUEST['submitMe'])
{
 $update_status=imw_query("update stub_tbl set patient_status='POS' where stub_id=' $stub_id'");
}
//END OF CODE OF UPDATING STUB TABLE

// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;
	$surgeonId = $detailConfirmation->surgeonId;
		unset($conditionArr);
		$conditionArr['usersId'] = $surgeonId;
		$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
		if($surgeonsDetails){
			foreach($surgeonsDetails as $usersDetail)
			{
				$signatureOfSurgeon = $usersDetail->signature;
			}
		}	
	$anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
		unset($conditionArr);
		$conditionArr['usersId'] = $anesthesiologist_id;
		$anesthesiologistDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
		if($anesthesiologistDetails){
		foreach($anesthesiologistDetails as $usersDetail){
			$anesthesiologistName = $usersDetail->fname.' '.$usersDetail->lname;
			$signatureOfAnesthesiologist = $usersDetail->signature;
		}
	}	
// GETTING CONFIRMATION DETAILS

//TIME saved in database
	 	   $Heparin_times=$_REQUEST['Heparin_time'];
	       $time_split = explode(" ",$Heparin_times);
	       
		if($time_split[1]=="PM" || $time_split[1]=="pm") {
			
			$time_split = explode(":",$time_split[0]);
			$medsTimeIncr=$time_split[0]+12;
			 $Heparin_time = $medsTimeIncr.":".$time_split[1].":00";
			
		}elseif($time_split[1]=="AM" || $time_split[1]=="am") {
		    $time_split = explode(":",$time_split[0]);
			$Heparin_time=$time_split[0].":".$time_split[1].":00";
			
			if($time_split[0]=="00" && $time_split[1]=="00") {
				$Heparin_time=$time_split[0].":".$time_split[1].":01";
			}
		}
	   //TIME saved in database
	



//GETTING CONFIRNATION DETAILS
	$getConfirmationDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if($getConfirmationDetails){
		extract($getConfirmationDetails);
	}
//GETTING CONFIRNATION DETAILS

$preopphy_patientConfirmSiteTempSite = $site;
// APPLYING NUMBERS TO PATIENT SITE
	if($preopphy_patientConfirmSiteTempSite == 1) {
		$preopphy_patientConfirmSiteTemp = "Left Eye";  //OD
	}else if($preopphy_patientConfirmSiteTempSite == 2) {
		$preopphy_patientConfirmSiteTemp = "Right Eye";  //OS
	}else if($preopphy_patientConfirmSiteTempSite == 3) {
		$preopphy_patientConfirmSiteTemp = "Both Eye";  //OU
	}else if($preopphy_patientConfirmSiteTempSite == 4) {
		$preopphy_patientConfirmSiteTemp = "Left Upper Lid";
	}else if($preopphy_patientConfirmSiteTempSite == 5) {
		$preopphy_patientConfirmSiteTemp = "Left Lower Lid";
	}else if($preopphy_patientConfirmSiteTempSite == 6) {
		$preopphy_patientConfirmSiteTemp = "Right Upper Lid";
	}else if($preopphy_patientConfirmSiteTempSite == 7) {
		$preopphy_patientConfirmSiteTemp = "Right Lower Lid";
	}else if($preopphy_patientConfirmSiteTempSite == 8) {
		$preopphy_patientConfirmSiteTemp = "Bilateral Upper Lid";
	}else if($preopphy_patientConfirmSiteTempSite == 9) {
		$preopphy_patientConfirmSiteTemp = "Bilateral Lower Lid";
	}else{
		$preopphy_patientConfirmSiteTemp = "Operative Eye";
	}
// END APPLYING NUMBERS TO PATIENT SITE


// GETTING IF PRE OP PHYSICIAN RECORD IS SAVED OR NOT
	$getPreOpPhyDetails = $objManageData->getRowRecord('preopphysicianorders', 'patient_confirmation_id', $pConfId);	
	
	$preOpPhysicianOrdersId = $getPreOpPhyDetails->preOpPhysicianOrdersId;
	$version_num 			=	$getPreOpPhyDetails->version_num;
	$versionDateTime	=	$getPreOpPhyDetails->version_date_time;
		
	$ivSelection = $getPreOpPhyDetails->ivSelection;
	$chbx_heparin_lock= $getPreOpPhyDetails->chbx_heparin_lock;
	$chbx_heparin_lockStart = $getPreOpPhyDetails->chbx_heparin_lockStart;
	$ivSelectionSide = $getPreOpPhyDetails->ivSelectionSide;
	$ivSelectionOther = $getPreOpPhyDetails->ivSelectionOther;
		
	$chbx_KVO= $getPreOpPhyDetails->chbx_KVO;
	$chbx_rate=$getPreOpPhyDetails->chbx_rate;
	$txtbox_rate=$getPreOpPhyDetails->txtbox_rate;
	$chbx_flu=$getPreOpPhyDetails->chbx_flu;
	$txtbox_flu=$getPreOpPhyDetails->txtbox_flu;
	$honanBallon = $getPreOpPhyDetails->honanBallon;
	$honanBallonTime = $getPreOpPhyDetails->honanBallonTime;
	$preOpOrdersOther = $getPreOpPhyDetails->preOpOrdersOther;	
	$Heparin_userid=	$getPreOpPhyDetails->Heparin_user;
	$relivednurse=     $getPreOpPhyDetails->relivednurse;
	$Heparin_time=   $getPreOpPhyDetails->Heparin_time;
	$comments=	$getPreOpPhyDetails->comments;
	$medicationStartTimeVal  = $getPreOpPhyDetails->medicationStartTime;
	$prefilMedicationStatus=	$getPreOpPhyDetails->prefilMedicationStatus;
	$anesthesiologistId =$getPreOpPhyDetails->anesthesiologistId;
	$prePhyFormStatus = $getPreOpPhyDetails->form_status;
	$saveFromChart = $getPreOpPhyDetails->saveFromChart;
	
	$signSurgeon1Id =$getPreOpPhyDetails->signSurgeon1Id;
	$signSurgeon1FirstName =$getPreOpPhyDetails->signSurgeon1FirstName;
	$signSurgeon1MiddleName =$getPreOpPhyDetails->signSurgeon1MiddleName;
	$signSurgeon1LastName =$getPreOpPhyDetails->signSurgeon1LastName;
	$signSurgeon1Status =$getPreOpPhyDetails->signSurgeon1Status;
	$signSurgeon1Name= $signSurgeon1LastName.','.$signSurgeon1FirstName;
		
	$signNurseId =$getPreOpPhyDetails->signNurseId;
	$signNurseFirstName =$getPreOpPhyDetails->signNurseFirstName;
	$signNurseMiddleName =$getPreOpPhyDetails->signNurseMiddleName;
	$signNurseLastName =$getPreOpPhyDetails->signNurseLastName;
	$NurseNameShow= $signNurseLastName.','.$signNurseFirstName;
	$signNurseStatus =$getPreOpPhyDetails->signNurseStatus;
	$notedByNurse =$getPreOpPhyDetails->notedByNurse;
	$evaluatedPatient = $getPreOpPhyDetails->evaluatedPatient;
	$signNurseDateTime =$getPreOpPhyDetails->signNurseDateTime;
	$signSurgeonDateTime =$getPreOpPhyDetails->signSurgeon1DateTime;
	
	$signNurse1Id =$getPreOpPhyDetails->signNurse1Id;
	$signNurse1FirstName =$getPreOpPhyDetails->signNurse1FirstName;
	$signNurse1MiddleName =$getPreOpPhyDetails->signNurse1MiddleName;
	$signNurse1LastName =$getPreOpPhyDetails->signNurse1LastName;
	$Nurse1NameShow= $signNurse1LastName.','.$signNurse1FirstName;
	$signNurse1Status =$getPreOpPhyDetails->signNurse1Status;
	$signNurse1DateTime =$getPreOpPhyDetails->signNurse1DateTime;
	
	//SHOW DETAIL OF PATIENT PRE OP MEDICATION
	$preOpPatientDetails = $objManageData->getArrayRecords('patientpreopmedication_tbl', 'patient_confirmation_id', $pConfId,'patientPreOpMediId','ASC');
	//SHOW DETAIL OF PATIENT PRE OP MEDICATION

	$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'preopphysicianorders' AND patient_conf_id = '$pConfId' ";
	$rsNotes =imw_query($query_rsNotes);
	$totalRows_rsNotes =imw_num_rows($rsNotes);
	
	$table = 'preopmedicationorder';
	$width = '300';
	
	if(!$getPreOpPhyDetails){
		unset($conditionArr);											
		if(count($preOpOrdersArr)>0){
			foreach($preOpOrdersArr as $preDefined){			
				$preOpMediDetails = $objManageData->getRowRecord('preopmedicationorder', 'medicationName', $preDefined);
				$strength = $preOpMediDetails->strength;
				$directions = $preOpMediDetails->directions;
				++$seq;
			}
		}
	}

	$table_row.=$head_table;
	$table_row.='<table cellpadding="0" cellspacing="0" style="width:700px; font-size:14px;border:1px solid #C0C0C0;margin-top:15px;">
		     	<tr><td colspan="3" width="700" style=" text-align:center; padding-top:5px; padding-bottom:5px; text-decoration:underline; font-size:16px; font-weight:bold;">Pre-Op Physician Orders</td></tr>';
	
	if($version_num <  2) 
	{
			$widthArr = array(230,100,100,230);
			$timeCol	=	true;
	}
	else
	{
			$widthArr = array(260,200,200,0);
			$timeCol	=	false;
	}
	
			
	$table_row.='			
				<tr>
					<td width="150" style="text-align:center;height:20px;background:#C0C0C0;font-weight:bold;">Pre Op Orders</td>
					<td width="400" style="text-align:center;height:20px;border-top:1px solid #C0C0C0;">On arrival the following drops will be given to the '.$preopphy_patientConfirmSiteTemp.'</td>					
					<td width="150" style="text-align:center;height:20px;background:#C0C0C0;">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" style="padding-top:10px;text-align:center;font-weight:bold;border-top:1px solid #C0C0C0;">List of Pre-Op Medication Orders</td>
				</tr>
				<tr>
					<td colspan="3" style="width:600px;  padding-bottom:15px;">
						<table style="width:600px;font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:'.$widthArr[0].'px;height:23px;padding-left:5px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Medication</td>
								<td style="width:'.$widthArr[1].'px;height:23px;padding-left:5px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Strength</td>
								<td style="width:'.$widthArr[2].'px;height:23px;padding-left:5px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Directions</td>
								'.($timeCol ? 
								'<td style="width:'.$widthArr[3].'px;height:23px;padding-left:5px; border-bottom:1px solid #C0C0C0;font-weight:bold;">Time</td>' : '')
								.'</tr>';
					if(count($preOpPatientDetails)>0)
					{
							foreach($preOpPatientDetails as $detailsOfMedication)
							{
								$parientPreOpMediOrderId = $detailsOfMedication->patientPreOpMediId;
								$preDefined = htmlentities($detailsOfMedication->medicationName);
								$strength = htmlentities($detailsOfMedication->strength);
								$directions = htmlentities($detailsOfMedication->direction);
								$timemeds[0] = $detailsOfMedication->timemeds;
								
								$timemeds[1] = $detailsOfMedication->timemeds1;
								$timemeds[2] = $detailsOfMedication->timemeds2;
								$timemeds[3] = $detailsOfMedication->timemeds3;
								$timemeds[4] = $detailsOfMedication->timemeds4;
								$timemeds[5] = $detailsOfMedication->timemeds5;
								$timemeds[6] = $detailsOfMedication->timemeds6;
								$timemeds[7] = $detailsOfMedication->timemeds7;
								$timemeds[8] = $detailsOfMedication->timemeds8;
								$timemeds[9] = $detailsOfMedication->timemeds9;
			
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
							
								
							 $table_row.='<tr>
							  <td style="padding:5px;width:'.$widthArr[0].'px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.$preDefined.'</td>
							  <td style="padding:5px;width:'.$widthArr[1].'px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.$strength.'</td>
							  <td style="padding:5px;width:'.$widthArr[2].'px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.$directions.'</td>';
							
							if($timeCol)
							{		 
								$table_row.='<td style="padding-left:3px;width:'.$widthArr[3].'px; border-bottom:1px solid #C0C0C0;vertical-align:top;">';		
								for($t=0;$t<=9;$t++)	
								{
									if($timemeds[$t]!=''){
										$table_row.=$timemeds[$t]."&nbsp;&nbsp;";
									}
									if($t==2){
										$table_row.="<br>";
									}
								}
								$table_row.='</td>';
							}
							$table_row.='</tr>'; 
						}
					}else{
						$table_row.='<tr>
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
							
					$table_row.='</table>
							</td>	
						</tr>';
	if($version_num <  2)
	{					
		$table_row.='
						<tr>
							<td colspan="3" style="padding-top:5px;padding-bottom:5px;border-top:1px solid #C0C0C0;">
								<table style="width:700px; font-size:14px;" cellpadding="0" cellspacing="0">
									<td style="width:70px;font-weight:bold;">Comments:&nbsp;</td>
									<td style="width:630px;">';
									if(trim($comments)){
										$table_row.=stripslashes($comments);
									}else{
										$table_row.="_______________________";	
									}
									$table_row.='</td>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="3" style="padding-top:5px;border-top:1px solid #C0C0C0;padding-bottom:5px;"><b>Start Heparin Lock:</b>';
							if($chbx_heparin_lockStart!=''){
								$table_row.="Yes";
							}else{
								$table_row.="___";	
							}
							$table_row.='&nbsp;&nbsp;<b>IV:</b>';
							if($chbx_heparin_lock!=''){
								$table_row.="Yes&nbsp;";
							}else{
								$table_row.="___&nbsp;";	
							}
							
							if($ivSelection!='' && $ivSelection!='other'){
								$table_row.="&nbsp;&nbsp;<b>".ucwords($ivSelection)."&nbsp;</b>".ucwords($ivSelectionSide);
							}
							if($ivSelection!='' && $chbx_heparin_lock!='' && $ivSelection!='other'){
								$table_row.="&nbsp;&nbsp;<b>KVO:</b>";
								if($chbx_KVO!=''){
									$table_row.="Yes&nbsp;";
								}else{
									$table_row.="___&nbsp;";	
								}
								$table_row.="&nbsp;<b>Rate:</b>";
								if($chbx_rate!=''){
									$table_row.="Yes&nbsp;";
								}
								
								if($txtbox_rate!=''){
									$table_row.=$txtbox_rate;
								}else{
									$table_row.="&nbsp;___&nbsp;";
								}
								$table_row.='/hr&nbsp;&nbsp;';
								$table_row.="&nbsp;<b>Flu:</b>";
								if($chbx_flu!=''){
									 $table_row.="Yes&nbsp;";
								}else{
									 $table_row.="___&nbsp;";	
								}
								
								if($txtbox_flu){
									$table_row.=$txtbox_flu;
								}
							}
							if($ivSelection=='other'){	 		
								$table_row.='&nbsp;<b>Other:</b>&nbsp;'.stripslashes($ivSelectionOther);
							}
							$table_row.='</td>
						</tr>';
		}
	
	$table_row.='
						
						<tr>
							<td width="150" style="padding-top:5px;padding-bottom:5px;border-top:1px solid #C0C0C0;font-weight:bold;">
								Other Pre-Op Orders:&nbsp;
							</td>
							<td width="550" style="padding-top:5px;padding-bottom:5px;border-top:1px solid #C0C0C0;" colspan="2">';
							if($preOpOrdersOther){
								$table_row.=stripslashes($preOpOrdersOther);
							}
							$table_row.='</td>
						</tr>
						<tr>
							<td width="700" colspan="3" style="border-top:1px solid #C0C0C0;padding-top:5px;">
								<table style="width:700px; font-size:14px;" cellpadding="0">
									
									<tr>
										<td style="width:250px;" valign="top"><b>Pre-Op orders noted by nurse:&nbsp;</b>
										'.(($notedByNurse==1) ? 'Yes' : '____').'
										</td>
										<td style="width:250px;vertical-align:top;" ><b>Nurse:&nbsp;</b>
										'.(($signNurse1Status=="Yes") ? $Nurse1NameShow : '_________').'
										<br><b>Electronically Signed:&nbsp;</b>
										'.(($signNurse1Status=="Yes") ? $signNurse1Status : '_________').'
										<br><b>Signature Date:&nbsp;</b>
										'.(($signNurse1Status=="Yes") ? $objManageData->getFullDtTmFormat($signNurse1DateTime) : '_________').'
										</td>
										<td style="width:200px;vertical-align:top;">&nbsp;</td>
									</tr>	
									
									';
									if($version_num > 2) {
										$evaluatedLabel = "I have evaluated the patient and determined they meet requirements for admission to the ASC for the proposed procedure and anesthesia.";
										if( $version_num > 3 ) {
											$evaluatedLabel = "I have evaluated the patient's medical records including related Diagnosis and Diagnostic tests prior to admission for surgery. The chosen order on this form reflect and are included as per the appropriate and best care on day of Surgery.";	
										}
										$table_row.='
									<tr>	
										<td colspan="3" style="width:695px;border-top:1px solid #C0C0C0;padding-top:5px;" valign="top" ><b>'.$evaluatedLabel.':&nbsp;</b>
											'.(($evaluatedPatient==1) ? 'Yes' : '____').'
										</td>
									</tr>	
										';
									}
									$table_row.='
									<tr>	
										<td style="width:250px;" valign="top"><b>Surgeon:&nbsp;</b>
										'.(($signSurgeon1Status=="Yes") ? 'Dr.'.$signSurgeon1Name : '_________').'
										<br><b>Electronically Signed:&nbsp;</b>
										'.(($signSurgeon1Status=="Yes") ? $signSurgeon1Status : '_________').'
										<br><b>Signature Date:&nbsp;</b>
										'.(($signSurgeon1Status=="Yes") ? $objManageData->getFullDtTmFormat($signSurgeonDateTime) : '_________').'
										</td>';
									
									if($version_num < 2)
									{	
									$table_row.='	
										<td style="width:250px;vertical-align:top;" ><b>Nurse:&nbsp;</b>
										'.(($signNurseStatus=="Yes") ? $NurseNameShow : '_________').'
										<br><b>Electronically Signed:&nbsp;</b>
										'.(($signNurseStatus=="Yes") ? $signNurseStatus : '_________').'
										<br><b>Signature Date:&nbsp;</b>
										'.(($signNurseStatus=="Yes") ? $objManageData->getFullDtTmFormat($signNurseDateTime) : '_________').'
										
										</td>';
									}
									else
									{
										$table_row.='<td style="width:250px;" valign="top">&nbsp;</td>';
									}	
									
									if($version_num < 2)
									{		
									$table_row.='
										<td style="width:200px;vertical-align:top;"><b>Relief Nurse:&nbsp;</b>
										';
										if($relivednurse!=''){
												$qry=imw_query("select lname,fname from users where usersId=$relivednurse");
												$res=imw_fetch_array($qry);
												$relivednursename=$res['lname'].','.$res['fname'];
												$table_row.=$relivednursename;
											}else{
												$table_row.="_________";
											}
						$table_row.='		
										</td>';
									}
									else
									{
										$table_row.='<td style="width:250px;" valign="top">&nbsp;</td>';
									}	
						$table_row.='				
									</tr>	
										
									
								</table>
							</td>
						</tr>
					</table>';
	
		//die($table_row);
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut = fputs($fileOpen,$table_row);
fclose($fileOpen);
$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';

if(($prePhyFormStatus=='completed' || $prePhyFormStatus=='not completed')){
?>

<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
	</tr>
</table>
 <form name="printpre_op_phy" action="new_html2pdf/createPdf.php?op=p" method="post">
 </form>

<script language="javascript">
	function submitfn(){
		document.printpre_op_phy.submit();
	}
</script>

<script type="text/javascript">
	submitfn();
</script>
<?php
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>