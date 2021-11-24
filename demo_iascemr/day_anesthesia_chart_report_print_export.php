<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$savePdfConfirmationId = isset($savePdfConfirmationId ) ? $savePdfConfirmationId  : "";
if(!$savePdfConfirmationId) {
	session_start();
	echo '<table id="loader_tbl" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">Please wait while data is retrieving from the server.</td>
			</tr>
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center"><img src="images/pdf_load_img.gif"></td> 
			</tr>
		</table>';
}
set_time_limit(900);

$table_pdf=$fac_con="";
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}
include_once("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
$blEnableHTMLGrid = false;
$strUserAgent = $_SERVER['HTTP_USER_AGENT'];//echo $strUserAgent;
if(stristr($strUserAgent, 'Safari') == true) {
	$blEnableHTMLGrid = true;
}
elseif(stristr($strUserAgent, 'MSIE') == true){
	$pos = strpos($strUserAgent, 'MSIE');
	(int)substr($strUserAgent,$pos + 5, 3);
	if((int)substr($strUserAgent,$pos + 5, 3) > 8){
		$blEnableHTMLGrid = true;
	}
}else if(stristr($_SERVER['HTTP_USER_AGENT'],"rv:11") !== false){ //IE 11
	$blEnableHTMLGrid = true;
}	

include_once("common_functions.php");
include_once("common/commonFunctions.php");
$tablename = "localanesthesiarecord";
include_once("admin/classObjectFunction.php");
include_once("library/classes/local_anesthesia.php");
$objLocalAnesData = new LocalAnesthesia;
$bckBottom = "4mm";
$headerBar='yes';
include_once("common/header_print_function.php");
include_once("imageSc/imgTimeInterval.php");
include_once("imageSc/imgGd.php");
global $objManageData;
$objManageData = new manageData;
$patientConfirmationIdSet = $_REQUEST['patientConfirmationId'];

if(!$patientConfirmationIdSet) { $patientConfirmationIdSet=0; }

if($savePdfConfirmationId) { $patientConfirmationIdSet=$savePdfConfirmationId; }//FROM export_anesthesia_chart.php

$patientConfirmationIdSingle = explode(",", $patientConfirmationIdSet);
$settings = $objManageData->loadSettings('asa_4,anes_mallampetti_score');

//START GET ALLERGIES
$allrgiesQry=("select allergy_name,reaction_name,patient_confirmation_id from patient_allergies_tbl where patient_confirmation_id IN (".$patientConfirmationIdSet.") ORDER BY patient_confirmation_id ASC, pre_op_allergy_id DESC");
$allergiesRes = imw_query($allrgiesQry) or die($allrgiesQry.imw_error());
$allergiesnum=@imw_num_rows($allergiesRes);
$p=0;
$detailsallergies = $allrgy_nameArr = $reaction_nameArr = $allrg_ptconfIdCheckArr = array();
while($detailsAllergy 	=@imw_fetch_array($allergiesRes)){
	$allrg_ptconfId 	= $detailsAllergy['patient_confirmation_id'];
	if(!in_array($allrg_ptconfId,$allrg_ptconfIdCheckArr)) {
		$p=0; 
	}
	$allrgy_nameArr[$allrg_ptconfId][$p]= $detailsAllergy['allergy_name'];
	$reaction_nameArr[$allrg_ptconfId][$p]= $detailsAllergy['reaction_name'];
	$p++;
	$allrg_ptconfIdCheckArr[] 				= $detailsAllergy['patient_confirmation_id'];
}

$detailsmed = $meds_nameArr = $meds_descArr = $meds_ptconfIdCheckArr = array();
$medsQry=("select prescription_medication_name,prescription_medication_desc, confirmation_id from patient_anesthesia_medication_tbl where confirmation_id IN (".$patientConfirmationIdSet.") ORDER BY confirmation_id ASC, prescription_medication_id DESC");
$medsRes = imw_query($medsQry);
$medsnum=@imw_num_rows($medsRes);
$r=0;
while($detailsMeds =@imw_fetch_array($medsRes)){
	$meds_ptconfId = $detailsMeds['confirmation_id'];
	if(!in_array($meds_ptconfId,$meds_ptconfIdCheckArr)) {
		$r=0; 
	}
	//echo '<br>'.$meds_ptconfId.' '.$r.' '.$detailsMeds['prescription_medication_name'];
	$meds_nameArr[$meds_ptconfId][$r]= $detailsMeds['prescription_medication_name'];
	$meds_descArr[$meds_ptconfId][$r]= $detailsMeds['prescription_medication_desc'];
	$r++;
	$meds_ptconfIdCheckArr[] 		 = $detailsMeds['confirmation_id'];
}
//exit();
//END GET ALLERGIES

$y=0;
$table_pdf='';
$selJoinQry = "SELECT 	pc.*, la.*, lm.*, lms.*, 
					date_format(la.signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia1DateTimeFormat , 
					date_format(la.signAnesthesia2DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia2DateTimeFormat , 
					date_format(la.signAnesthesia3DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia3DateTimeFormat ,
					date_format(la.signAnesthesia4DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia4DateTimeFormat ,
					intra_us.lname AS intra_nurse_lname, intra_us.fname AS intra_nurse_fname,
					post_us.lname AS post_nurse_lname, post_us.fname AS post_nurse_fname,
					preNrs.patientHeight AS ptHght, preNrs.patientWeight AS ptWght,
					preNrs.ivSelection AS ivSelectionPreNrs, preNrs.ivSelectionOther AS ivSelectionOtherPreNrs,
					preNrs.ivSelectionSide AS ivSelectionSidePreNrs, preNrs.gauge AS gaugePreNrs, preNrs.gauge_other AS gauge_otherPreNrs
				FROM localanesthesiarecord la
				INNER JOIN patientconfirmation pc ON pc.patientConfirmationId = la.confirmation_id
				LEFT JOIN localanesthesiarecordmedgrid lm ON pc.patientConfirmationId = lm.confirmation_id
				LEFT JOIN localanesthesiarecordmedgridsec lms ON pc.patientConfirmationId = lms.confirmation_id
				LEFT JOIN users intra_us ON intra_us.usersId = la.relivedIntraNurseId
				LEFT JOIN users post_us ON post_us.usersId = la.relivedPostNurseId
				LEFT JOIN preopnursingrecord preNrs ON preNrs.confirmation_id = la.confirmation_id
				LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=pc.patientConfirmationId
				WHERE la.confirmation_id IN (".$patientConfirmationIdSet.")
				AND stub_tbl.patient_confirmation_id!=''
				$fac_con
				GROUP BY la.confirmation_id
				ORDER BY pc.dos
				";
$selJoinRes = imw_query($selJoinQry) or die(imw_error());

while($selJoinRow = imw_fetch_assoc($selJoinRes)) {
	extract($selJoinRow);
	//echo '<pre>';print_r($selJoinRow);  continue;
	if($patientConfirmationId)
	{
		//echo "Value: $value<br />\n";
		//$pConfId = $value;
		$pConfId = $patientConfirmationId;
		$finalizeStatus = $selJoinRow['finalize_status'];	
		$surgeonId = $selJoinRow['surgeonId'];
		$anesthesiologist_id = $selJoinRow['anesthesiologist_id'];
		$confimDOS=$selJoinRow['dos'];
		$orStartTime=calculate_timeFun($orStartTime); //CODE TO DISPLAY OR START TIME
		$orStopTime=calculate_timeFun($orStopTime); //CODE TO DISPLAY OR STOP TIME
		$startTime=calculate_timeFun($startTime); //CODE TO DISPLAY START TIME
		$stopTime=calculate_timeFun($stopTime); //CODE TO DISPLAY STOP TIME
		$newStartTime2=calculate_timeFun($newStartTime2); //CODE TO DISPLAY STOP TIME
		$newStopTime2=calculate_timeFun($newStopTime2); //CODE TO DISPLAY STOP TIME
		$newStartTime3=calculate_timeFun($newStartTime3); //CODE TO DISPLAY STOP TIME
		$newStopTime3=calculate_timeFun($newStopTime3); //CODE TO DISPLAY STOP TIME
		$localanesFormStatus = $form_status;
		
		$appletData= $selJoinRow['applet_data'];
		$appletTimeInterval= $selJoinRow['applet_time_interval'];
		
		if(!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) { $version_num	=	1; }
		else if(!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') { $version_num	=	2; }
		
		if($pConfId) 
		{
				extract($_GET);
				$headerData = headerInfo($pConfId); 
				$table_pdf.= $headerData;
				
				$get_http_path=$_REQUEST['get_http_path'];
				
				//Alert and Oriented values
				if($alertOriented=="1") { $alert_oriented_name="Oriented x3"; }
				if($alertOriented=="2") { $alert_oriented_name="Oriented x2"; }
				//if($alertOriented=="3") { $alert_oriented_name="Awake"; }
				if($alertOriented=="4") { $alert_oriented_name="Confused";}
				if($alertOriented=="5") { $alert_oriented_name="Disoriented"; }
				if($alertOriented=="6") { $alert_oriented_name="Combative"; }
				//Alert and Oriented values END
				
				$siteShow = "";
				$siteTemp = $site; // FROM PATIENT CONFIRMATION TABLE
				// APPLYING NUMBERS TO PATIENT SITE
				if($siteTemp == 1) {
					$siteShow = "Left Eye";  //OS
				}else if($siteTemp == 2) {
					$siteShow = "Right Eye";  //OD
				}else if($siteTemp == 3) {
					$siteShow = "Both Eye";  //OU
				}
				// END APPLYING NUMBERS TO PATIENT SITE	
				
				
				if($anes_ScanUploadPath || $anes_ScanUpload)
				{
					$scnImgSrc = 'new_html2pdf/anesScanUpld'.$localAnesthesiaRecordId.'pg.jpg';
					$newImages='';
					if($anes_ScanUploadType == 'application/pdf') 
					{
						$countPdfPages =  $objManageData->getNumPagesPdf($anes_ScanUploadPath);
						$path = realpath(dirname(__FILE__));
						exec("convert ".$anes_ScanUploadPath." new_html2pdf/anesScanUpld".$localAnesthesiaRecordId."pg%d.jpg");
						for($k=0; $k<$countPdfPages;$k++) 
						{
							$newSize=' width="620" height="650"';
							$scnImgPdfSrc="anesScanUpld".$localAnesthesiaRecordId."pg".$k.".jpg";
							if($savePdfConfirmationId) {
								$scnImgPdfSrc="new_html2pdf/anesScanUpld".$localAnesthesiaRecordId."pg".$k.".jpg";
							}
							
							$newImages.='
							<tr >
								<td width="100%"><img style="border:none; cursor:pointer;"  src="'.$scnImgPdfSrc.'"   '.$newSize.' ></td>
							</tr>';
						}
						
						if(!$anes_ScanUploadPath && $anes_ScanUpload)
						{//CODE TO SHOW OLD SAVED RECORDS
							$scnImgSrc = "admin/logoImg.php?from=local_anesthesia_record&id=".$localAnesthesiaRecordId;	
							$newImages.='
							<tr >
								<td width="100%"><img style="border:none; cursor:pointer;"  src="'.$scnImgSrc.'" ></td>
							</tr>';
						}
					}
					else 
					{
						if(!$anes_ScanUploadPath && $anes_ScanUpload)
						{//CODE TO SHOW OLD SAVED RECORDS
							$bakImgResource = imagecreatefromstring($anes_ScanUpload);
							imagejpeg($bakImgResource,$scnImgSrc);
							$file=fopen($scnImgSrc,'w+');
							fputs($file,$anes_ScanUpload);
						}
						else if($anes_ScanUploadPath) 
						{
							copy($anes_ScanUploadPath,$scnImgSrc);
						}
						$newSize=' width="150" height="100"';
						$priImageSize=array();
						if(file_exists($scnImgSrc))
						{
							$priImageSize = getimagesize($scnImgSrc);
							if($priImageSize[0] > 395 && $priImageSize[1] < 840)
							{
								$newSize = $objManageData->imageResize(680,400,710);						
								$priImageSize[0] = 710;
							}
							elseif($priImageSize[1] > 840)
							{
								$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],700);						
								$priImageSize[1] = 700;
							}
							else
							{					
								$newSize = $priImageSize[3];
							}							
							if($priImageSize[1] > 800 )
							{
								echo '<newpage>';
							}
						}
						
						$scnImgPdfSrc = 'anesScanUpld'.$localAnesthesiaRecordId.'pg.jpg';
						if($savePdfConfirmationId) {
							$scnImgPdfSrc = 'new_html2pdf/anesScanUpld'.$localAnesthesiaRecordId.'pg.jpg';
						}
						
						$newImages.='
							<tr >
								<td width="100%"><img style="border:none; cursor:pointer;"  src="'.$scnImgPdfSrc.'"   '.$newSize.'></td>
							</tr>';
						}
						
				}
			
				//CODE TO SET $apTime 
				if($apTime=="00:00:00" || $apTime=="") 
				{
					$apTime="";
				}
				else
				{
					$apTime=$apTime;
					
					$time_split_apTime = explode(":",$apTime);
					if($time_split_apTime[0]>=12)
					{
						$am_pm = "PM";
					}
					else
					{
						$am_pm = "AM";
					}
					if($time_split_apTime[0]>=13) 
					{
						$time_split_apTime[0] = $time_split_apTime[0]-12;
						if(strlen($time_split_apTime[0]) == 1)
						{
							$time_split_apTime[0] = "0".$time_split_apTime[0];
						}
					}
					else 
					{
						//DO NOTHNING
					}
					//echo $time_split_apTime[1];
					$apTime = $time_split_apTime[0].":".$time_split_apTime[1]." ".$am_pm;
				}
				//END CODE TO SET apTime
				
				
				
				$table_pdf.='<table style="width:744px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">';
				
				$table_pdf.='
					<tr>
						<td style="width:744px;" class="fheader">MAC/Local/Regional Anesthesia Record</td>
					</tr>';
					if($anes_ScanUploadPath || $anes_ScanUpload)
					{
						$table_pdf.='
							<tr>	
								<td style="width:700px;text-align:center;">'.$newImages.'</td>
							</tr>';
					}
					else
					{
						
						if($version_num > 1) 
						{
						$table_pdf.='
						<tr>
							<td style="width:744px;" class="bold bdrtop bgcolor">The following items were verified before Induction of Anesthesia</td>
						</tr>
						<tr>	
							<td style="width:740px;" valign="middle">
								<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
									<tr>
										<td style="width:280px;">
											<strong>Nurse and anesthesia care provider confirm:&nbsp;</strong>
										</td>
										<td style="width:200px; vertical-align:top;text-align:left;">';
										if($signAnesthesia4Id)
										{
											$table_pdf.='<b>Anethesia Provider:</b> '.$signAnesthesia4LastName.", ".$signAnesthesia4FirstName." ".$signAnesthesia4MiddleName."<br>
												<b>Electronically Signed:&nbsp;</b>Yes"; 
										}else {
											$table_pdf.='__________';
										}	
									
						$table_pdf.='
										</td>
										<td style="width:235px;vertical-align:top;text-align:right; white-space:nowrap">';
										$getReliefNurseName = "";
						$table_pdf.="<b>Relief Nurse / Anesthesia:</b><br>";
										if($reliefNurseId){
											$getReliefNurseAnesName = getUsrNm($reliefNurseId);
											$table_pdf.=$getReliefNurseAnesName[0];   
										}else{
											$table_pdf.='_________';	
										}	
						$table_pdf.='
										</td>
									</tr>';
						if($signAnesthesia4Id)
						{
						$table_pdf.='	
									<tr>
										<td>&nbsp;</td>
										<td colspan="2">
											<b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signAnesthesia4DateTime).'
										</td>
									</tr>';
						}
						$table_pdf.='
								</table>
							</td>
						</tr>	
						<tr><td style="border-bottom:dashed 1px #C0C0C0">&nbsp;</td></tr>
						<tr>	
							<td style="width:740px;" valign="middle">
								<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
									<tr>
										<td class="bdrbtm" style="width:320px;">Confirmation of: identify, procedure, procedure site and consent(s)</td>
										<td class="bdrbtm cbold" style="text-align:center; width:50px; style="border-right:1px solid #C0C0C0;"">';
										if($confirmIPPSC_signin) { $table_pdf.=stripslashes($confirmIPPSC_signin); }else{$table_pdf.="___"; }
										$table_pdf.='</td>
										<td class="bdrbtm" style="text-align:left;width:320px; ">Site marked by person performing the procedure</td>
										<td class="bdrbtm cbold" style="width:50px;"> ';
										if($siteMarked) { $table_pdf.=stripslashes($siteMarked); }else{$table_pdf.="___"; }
										$table_pdf.='</td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>	
							<td style="width:740px;" valign="middle">
								<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
									<tr>
										<td class="bdrbtm" style="width:320px;">Patient allergies</td>
										<td class="bdrbtm cbold" style="text-align:center; width:50px; style="border-right:1px solid #C0C0C0;"">';
										if($patientAllergies) { $table_pdf.=stripslashes($patientAllergies); }else{$table_pdf.="___"; }
										$table_pdf.='</td>
										<td class="bdrbtm" style="text-align:left;width:320px; ">Difficult airway or aspiration risk?</td>
										<td class="bdrbtm cbold" style="width:50px;"> ';
										if($difficultAirway) { $table_pdf.=stripslashes($difficultAirway); }else{$table_pdf.="___"; }
										$table_pdf.='</td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>	
							<td style="width:740px;" valign="middle">
								<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
									<tr>
										<td class="bdrbtm" style="width:320px;">
											<table cellpadding="0" cellspacing="0" style="width:320px;">	
												<tr>
													<td style="width:200px;">Risk of blood loss (>500 ml)</td>
												</tr>
												<tr>
													<td style="width:200px;padding-left:10px;">';
														if($riskBloodLoss=="Yes"){
															$table_pdf.='<b># of units available:&nbsp;</b>';
															$table_pdf.=htmlentities(stripslashes($bloodLossUnits)); 
															if(!$bloodLossUnits){$table_pdf.="____";}
														}else{ $table_pdf.="&nbsp;"; }
														
													$table_pdf.='
													</td>
												</tr>
											</table>
										</td>
										<td class="bdrbtm cbold" style="text-align:center; width:50px; style="border-right:1px solid #C0C0C0;"">';
										 if($riskBloodLoss) { $table_pdf.=stripslashes($riskBloodLoss);}else{$table_pdf.="__"; }
										$table_pdf.='</td>
										
										
										<td class="bdrbtm" style="text-align:left;width:320px; ">Anesthesia safety check completed</td>
										<td class="bdrbtm cbold" style="width:50px;"> ';
										if($anesthesiaSafety) { $table_pdf.=stripslashes($anesthesiaSafety); }else{$table_pdf.="___"; }
										$table_pdf.='</td>
									</tr>
								</table>
							</td>
						</tr>
						
						<tr>	
							<td style="width:740px; border-bottom:dashed 1px #C0C0C0" valign="middle"><b>Briefing:</b></td></tr>
							
						<tr>	
							<td style="width:740px;" valign="middle">
								<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
									<tr>
										
										<td class="bdrbtm" style="text-align:left;width:320px; ">All members of the team have discussed care plan and addressed concerns</td>
										<td class="bdrbtm cbold" style="width:50px;"> ';
										if($allMembersTeam) { $table_pdf.=stripslashes($allMembersTeam); }else{$table_pdf.="___"; }
										$table_pdf.='</td>
										<td class="bdrbtm" style="width:320px;">&nbsp;</td>
										<td class="bdrbtm cbold" style="text-align:center; width:50px; style="border-right:1px solid #C0C0C0;"">';
										$table_pdf.='&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						';
						}
					
						$table_pdf.='
							<tr>
								<td style="width:744px;" class="bold bdrtop bgcolor">Pre-Operative</td>
							</tr>
							<tr>	
								<td style="width:740px;" class="bdrtop">
									<table style="width:740px;;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:170px; text-align:left" class="bdrbtm"><b>Patient Interviewed:</b>
						';
											if($patientInterviewed=="Yes") {
												$table_pdf.="Done";
											}else{$table_pdf.="___";}
						$table_pdf.='
											</td>
											<td style="width:170px;" class="bdrbtm"><b>No change in H&amp;P:</b>&nbsp;
						';
											if($chartNotesReviewed=="Yes"){
												$table_pdf.="Done";
											}else{$table_pdf.="___";}
						$table_pdf.='
											</td>
											<td style="width:180px" class="bdrbtm">';
											if($version_num > 5) {
												$table_pdf.='
												<b>Changes in H&amp;P documented:</b>&nbsp;';
												if($chartNotesReviewed=='Changed'){
													$table_pdf.="Done";
												}else{$table_pdf.="___";}
											}
											$table_pdf.='
											</td>
											<td style="width:200px;" class="bdrbtm">&nbsp;<b>Alert and Awake:</b>&nbsp;
						';
											if(trim($alertOriented)){
												$table_pdf.=trim($alert_oriented_name);
											}else{$table_pdf.="___";}
						$table_pdf.='
											</td>
										</tr>
									</table>
								</td>
							</tr>
						';
						
						
						$table_pdf.='
							<tr>
								<td style="width:740px;">
									<table style="width:740px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:130px;" class="bdrbtm bold">Procedure Verified:</td>
											<td style="width:200px;" class="bdrbtm">
						';
											if($patient_primary_procedure) {
												$table_pdf.=wordwrap($patient_primary_procedure,40,"<br>",1);
											}else{$table_pdf.="________";}
						$table_pdf.=
											'</td>
											<td style="width:130px;" class="bdrbtm bold">Secondary Verified:</td>
											<td style="width:200px;" class="bdrbtm">
						';
											if($procedureSecondaryVerified){	
												$table_pdf.=wordwrap($patient_secondary_procedure);
											}else{$table_pdf.="________";}
						$table_pdf.='
											</td>
										</tr>
										<tr>
											<td colspan="2" class="bdrbtm"><b>Site Verified '.$siteShow.':&nbsp;</b>
						';
											if($siteVerified!=''){ $table_pdf.=$siteVerified; }
											if($version_num > 2) {
												$table_pdf.="&nbsp;&nbsp;&nbsp;<b>NPO:&nbsp;</b>";
												if($npo!=''){
													$table_pdf.="Done";
												}else{$table_pdf.="_____";}	
											}
											else{$table_pdf.="_____";}
						$table_pdf.='
											</td>
											<td colspan="2" class="bdrbtm"><b>Assisted by Translator:&nbsp;</b>
						';
											if($assistedByTranslator=='yes') { $table_pdf.="Done"; }
											else{$table_pdf.="_____";}
											if($version_num > 2 && ($settings['anes_mallampetti_score'] || trim($mallampetti_score))) {
												$table_pdf.="&nbsp;&nbsp;&nbsp;<b>Mallampetti Score:&nbsp;</b>";
												if($mallampetti_score!=''){
													$table_pdf.=$mallampetti_score;
												}else{$table_pdf.="_____";}	
											}											
						$table_pdf.='
											</td>
										</tr>
										<tr>
											<td colspan="2" style="width:350px;" class="bdrbtm"><b>Pt reassessed, stable<br>for anesthesia / surgery:</b>&nbsp;';
											if($fpExamPerformed=='Yes'){
												$table_pdf.="Done";
											}else{$table_pdf.="___";}
											$table_pdf.='
											</td>
											<td colspan="2" style="width:350px;" class="bdrbtm">';
											if($version_num > 4) {
												$ivSelection 		= ucfirst($ivSelectionPreNrs);
												$ivSelectionOther 	= stripslashes($ivSelectionOtherPreNrs);
												$ivSelectionVal 	= (strtolower($ivSelection)=='other') ? $ivSelectionOther : $ivSelection;
												
												$ivSelectionSide 	= ucfirst($ivSelectionSidePreNrs);
												
												$gauge 				= $gaugePreNrs;
												$gauge_other 		= $gauge_otherPreNrs;
												$gaugeVal 			= (strtolower($gauge)=='other') ? $gauge_other : $gauge;
												
												$table_pdf.='<b>IV:&nbsp;</b>';
												if($ivSelectionVal){
													$table_pdf.=$ivSelectionVal;
												}else{$table_pdf.="_____";}
												if(strtolower($ivSelection)!='other') {
													$table_pdf.="&nbsp;&nbsp;&nbsp;<b>Right/Left:&nbsp;</b>";
													if($ivSelectionSide && $ivSelection){
														$table_pdf.=$ivSelectionSide;
													}else{$table_pdf.="_____";}
													$table_pdf.="&nbsp;&nbsp;&nbsp;<b>Gauge:&nbsp;</b>";
													if($gaugeVal && $ivSelection){
														$table_pdf.=$gaugeVal;
													}else{$table_pdf.="_____";}	
												}
											}
											$table_pdf.=
											'</td>
										</tr>										
										<tr>
											<td colspan="2"  style="width:350px;" class="pl5 bold bdrbtm bgcolor">Allergies</td>
											<td colspan="2"  style="width:350px;" class="pl5 bold bdrbtm bgcolor">Medications</td>
										</tr>
										<tr>
											<td colspan="2"  style="width:350px;vertical-align:top;">
												<table style="width:350px;" cellpadding="0" cellspacing="0">
													<tr>
														<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Name</td>
														<td style="width:145px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Reaction</td>
													</tr>
						';
													$allrgiesQry=("select allergy_name,reaction_name from patient_allergies_tbl where patient_confirmation_id=$pConfId");
													$allergiesRes = imw_query($allrgiesQry);
													$allergiesnum=@imw_num_rows($allergiesRes);
													if($allergiesnum>0)
													{
														while($detailsAllergy=@imw_fetch_array($allergiesRes))
														{
															$table_pdf.='
																<tr>
																	<td style="width:200px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes($detailsAllergy['allergy_name']).'</td>
																	<td style="width:140px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes($detailsAllergy['reaction_name']).'</td>
																</tr>
															';
														}
													}
													else
													{
														$table_pdf.='
															<tr>
																<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
																<td style="width:150px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
															</tr>
															<tr>
																<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
																<td style="width:150px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
															</tr>
															<tr>
																<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
																<td style="width:150px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
															</tr>
														';	
													}
						$table_pdf.='
												</table>
											</td>
											
											<td colspan="2" style="width:350px;vertical-align:top;">
												<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
															<tr>
																<td style="width:125px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Name</td>
																<td style="width:100px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Dosage</td>
																<td style="width:100px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Sig</td>
															</tr>';
													$medsQry=("select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_anesthesia_medication_tbl where confirmation_id=$pConfId");
													$medsRes = imw_query($medsQry);
													$medsnum=@imw_num_rows($medsRes);
													if($medsnum>0){
														while($detailsMeds=@imw_fetch_array($medsRes)){
														$table_pdf.='
															<tr>
																<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.$detailsMeds['prescription_medication_name'].'</td>
																<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.$detailsMeds['prescription_medication_desc'].'</td>
																<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid">'.$detailsMeds['prescription_medication_sig'].'</td>
															</tr>';														
														}
													}else {
														for($q=1;$q<=3;$q++) {
															$table_pdf.='
															<tr>
																<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid">&nbsp;</td>
																<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid">&nbsp;</td>
																<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid">&nbsp;</td>
															</tr>';
														}
															
													}
											$table_pdf.='		
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>';
							
							
						$table_pdf.='
							<tr>
								<td style="width:700px;" >
									<table style="width:700px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:120px;" class="bdrbtm"><b>Time:&nbsp;</b>';
											if($bp_p_rr_time <> '00:00:00' && !empty($bp_p_rr_time)){
												$bp_p_rr_time=$objManageData->getTmFormat($bp_p_rr_time);
											}else{
												$bp_p_rr_time	=	'';	
											}
											
											if($bp_p_rr_time){
												$table_pdf.=$bp_p_rr_time;
											}else{$table_pdf.="___";}
											$table_pdf.=
											'</td>
											<td style="width:70px;" class="bdrbtm"><b>BP:&nbsp;</b>
						';
											if($bp) { $table_pdf.=$bp; }
											else{$table_pdf.="___"; }
						$table_pdf.='
											</td>
											<td style="width:70px;" class="bdrbtm"><b>P:&nbsp;</b>
						';
											if($P){ $table_pdf.=$P;}
											else{$table_pdf.="___";}
						$table_pdf.='
											</td>
											<td style="width:70px;" class="bdrbtm"><b>RR:&nbsp;</b>
						';
											if($rr){$table_pdf.=$rr;}
											else{$table_pdf.="___";}
						$table_pdf.='
											</td>
											<td style="width:70px;" class="bdrbtm"><b>SaO<sub>2</sub>:&nbsp;</b>
						';
											if($sao){$table_pdf.=$sao;}
											else{$table_pdf.="___";}
						$table_pdf.='
											</td>
											<td colspan="2" style="width:300px;" class="bold bdrbtm">&nbsp;</td>
										</tr>
										<tr>
											<td style="width:120px;" class="bold bdrbtm">Evaluation:&nbsp;';
						$table_pdf.=
											'</td>
											<td colspan="4" style="width:280px;" class="bdrbtm">';
											if(trim($evaluation2)){
												$table_pdf.=htmlentities(stripslashes($evaluation2));
											}else{$table_pdf.="_____";}
						$table_pdf.='
											</td>';
						$table_pdf.='
											<td style="width:80px;" class="bold bdrbtm">';
											if($version_num > 2) {
												$table_pdf.='Dentition:&nbsp;';
											}
						$table_pdf.=
											'</td>
											<td style="width:220px;" class="bdrbtm">';
											if($version_num > 2) {
												if(trim($dentation)){
													$table_pdf.=htmlentities(stripslashes($dentation));
												}else{$table_pdf.="_____";}
											}
						$table_pdf.=
											'</td>';
											
						$table_pdf.='
										</tr>
										<tr>
											<td colspan="5" style="width:400px;" class="bdrbtm">
												<b>Stable cardiovascular and Pulmonary function:&nbsp;</b>
						';
												if(trim($stableCardiPlumFunction)){$table_pdf.="Done";}
												else{$table_pdf.="_____";}	
						$table_pdf.='
											</td>
											<td colspan="2" style="width:315px;" class="bdrbtm">
												<b>Blood Sugar:&nbsp;</b>
						';
												if($NA!='' || $bsValue!=''){
													if($NA=='1') { $bsValue='NA'; }
													$table_pdf.=$bsValue;
												}
												else{$table_pdf.="_____";}
						$table_pdf.='
											</td>
										</tr>
										<tr>
											<td  colspan="7" class="bdrbtm" style="font-size:13px;width:700px;">
												<b>Plan regional anesthesia with sedation.Risks,benefits and alternatives of anesthesia plan have been discussed:</b>
						';
												if($planAnesthesia!=''){$table_pdf.="Done";}
												else{$table_pdf.="____";}
						$table_pdf.='
											</td>
										</tr>
										<tr>
											<td  colspan="5" style="width:400px;" class="bdrbtm">
												<b>All questions answered:&nbsp;</b>
						';
												if($allQuesAnswered){$table_pdf.=$allQuesAnswered;}
												else{$table_pdf.="____";}
												
												$val="___";
												if($asaPhysicalStatus!=''){
													$val=$asaPhysicalStatus;
													if($asaPhysicalStatus=='1'){
											   			$val='I';
													}
													if($asaPhysicalStatus=='2'){
													   $val='II';
													}
													if($asaPhysicalStatus=='3'){		
													   $val='III';
													}
													if($asaPhysicalStatus=='4'){		
													   $val='IV';
													}
												}
						$table_pdf.="&nbsp;&nbsp;&nbsp;<b>ASA Physical Status:&nbsp;</b>".$val;
						$table_pdf.='
											</td>
											<td style="width:300px;" colspan="2" class="bdrbtm">
						';
											if($signAnesthesia1Status=="Yes")
											{
												$table_pdf.="<br><b>Anesthesia Provider:&nbsp;</b>".$signAnesthesia1LastName.', '.$signAnesthesia1FirstName;
												$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>Yes";
												$table_pdf.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signAnesthesia1DateTime);
											}
											else 
											{
												$table_pdf.="<br><b>Anesthesia Provider:&nbsp;</b>________";
												$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>________";
												$table_pdf.="<br><b>Signature Date:&nbsp;</b>________";
											}
						$table_pdf.='
											</td>
										</tr>
									</table>
								</td>
							</tr>
							';
						$table_pdf.='
							
						';
						$dosageArr	=	array('blank3','blank4','blank1','blank2','propofol','midazolam','ketamine','labetalol','Fentanyl','spo2','o2lpm');
						foreach($dosageArr as $dosage)
						{
							for($L = 1; $L <= 20 ; $L++)
							{
								$var			=	trim($dosage).'_'.$L;
								$t_var		=	't_'.$var;
								$tempArr	=	explode('@@',$$var);
								$$var		=	$tempArr[0];
								$$t_var		=	$tempArr[1];
								unset($tArr); $date = $time = '';
								$tArr		=	explode(" ",$$t_var);
								$date		=	trim($tArr[0]);
								$date		=	($date)	?	date('m/d/y',strtotime($date))	:	''	;
								$time		=	trim($tArr[1]).trim($tArr[2]);	
								$time		=	($time)	?	$objManageData->getTmFormat($time)	:	''	;
								$$t_var		=	$date.' ' .$time;
								
								//echo $var.' : '.$$var.' * ' .$t_var .' : '.$$t_var .'<br>';
							}
						}
						
					
					$table_pdf.='
						<tr>
							<td style="width:700px; verticle-align:top;">
								<table style="width:700px;" cellpadding="0" cellspacing="0">
									<tr>
										<td class="bold bgcolor bdrbtm">Holding area through Intra-Op</td>
									</tr>
								<tr>	
									<td style="width:700px;vertical-align:top;">
										<table style="width:700px;border:1px solid #C0C0C0;font-size:12px;" cellpadding="0" cellspacing="0">
											<tr>';		
											if( defined("ANES_OR_START_STOP_TIME") && constant("ANES_OR_START_STOP_TIME")=="YES") {
												$table_pdf.='	
												<td style="width:100px; text-align:center;">
													<b>OR Start Time</b><br>';
													if($orStartTime){
														$table_pdf.=$orStartTime;	
													}else{$table_pdf.="____";}
												$table_pdf.=	
												'</td>
												<td style="width:100px; text-align:center;">
													<b>OR Stop Time</b><br>';
													if($orStopTime){
														$table_pdf.=$orStopTime;	
													}else{$table_pdf.="____";}
												$table_pdf.=	
												'</td>';
											}
											$table_pdf.='		
												<td style="width:100px; text-align:center;">
													<b>Anes&nbsp;Start&nbsp;Time</b><br>';
													if($startTime){
														$table_pdf.=$startTime;	
													}else{$table_pdf.="____";}
												$table_pdf.=	
												'</td>
												<td style="width:100px; text-align:center;" >
													<b>Anes&nbsp;Stop&nbsp;Time</b><br>';
													if($stopTime){
														$table_pdf.=$stopTime;	
													}else{$table_pdf.="____";}
												$table_pdf.=	
												'</td>';
											if( defined("ANES_OR_START_STOP_TIME") && constant("ANES_OR_START_STOP_TIME")!="YES") {
												$table_pdf.='	
												<td style="width:100px; text-align:left;"></td>
												<td style="width:100px; text-align:left;"></td>';	
											}
											$table_pdf.='	
												<td style="width:75px; text-align:center;">
													<b>Start Time</b><br>';
													if($newStartTime2){
														$table_pdf.=$newStartTime2;	
													}else{$table_pdf.="____";}
												$table_pdf.=	
												'</td>
												<td style="width:75px; text-align:center;">
													<b>Stop Time</b><br>';
													if($newStopTime2){
														$table_pdf.=$newStopTime2;	
													}else{$table_pdf.="____";}
												$table_pdf.=	
												'</td>
												<td style="width:75px; text-align:center;">
													<b>Start Time</b><br>';
													if($newStartTime3){
														$table_pdf.=$newStartTime3;	
													}else{$table_pdf.="____";}
												$table_pdf.=	
												'</td>
												<td style="width:75px; text-align:center;" >
													<b>Stop Time</b><br>';
													if($newStopTime3){
														$table_pdf.=$newStopTime3;	
													}else{$table_pdf.="____";}
												$table_pdf.=	
												'</td>
											</tr>
										</table>
									</td>
								</tr>
									
									<tr>	
										<td style="width:700px;vertical-align:top;">
											<table style="width:700px;font-size:12px;" cellpadding="0" cellspacing="0">';
												
												foreach($dosageArr as $dosage)
												{
													$dosage=	trim($dosage);
													
													if( $dosage == 'blank1')				$medLabel=$blank1_label;
													elseif( $dosage == 'blank2')			$medLabel=$blank2_label;
													elseif( $dosage == 'blank3')			$medLabel=$blank3_label;
													elseif( $dosage == 'blank4')			$medLabel=$blank4_label;
													elseif( $dosage == 'propofol')		$medLabel=$mgPropofol_label;
													elseif( $dosage == 'midazolam')	$medLabel=$mgMidazolam_label;
													elseif( $dosage == 'ketamine')		$medLabel=$mgKetamine_label;
													elseif( $dosage == 'labetalol')		$medLabel=$mgLabetalol_label;
													elseif( $dosage == 'Fentanyl')		$medLabel=$mcgFentanyl_label;
													elseif( $dosage == 'spo2')			$medLabel="SaO<sub>2</sub>";
													elseif( $dosage == 'o2lpm')			$medLabel="O<sub>2</sub>l/m";
													
													if($dosage <> 'spo2' && $dosage <> 'o2lpm' ){ $medLabel=htmlentities($medLabel);}
													$label			=	trim($dosage).'_label';
													$table_pdf	.=	'<tr><td style="width:50px;border-right:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0; height:25px;font-weight:bold;vertical-align:middle;">'.$medLabel.'</td>';	
													
													for($L = 1; $L <= 20 ; $L++)
													{
														$var			=	trim($dosage).'_'.$L;
														$t_var		=	't_'.$var;
														$table_pdf.="<td style='width:25px; border-right:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0; height:25px;font-weight:bold;text-align:center;vertical-align:middle;'>".htmlentities($$var)."<br /><span style=\"font-size:8px; font-weight:normal; \">".htmlentities($$t_var)."</span></td>";
													}
													$table_pdf.='</tr>';
												}
												
												
												if(stripslashes($ekgBigRowValue))
												{
													$table_pdf.='
															<tr>
																<td colspan="21" class="bdrbtm" style="vertical-align:bottom;">
																	<b>EKG:</b>&nbsp;'.stripslashes($ekgBigRowValue).'</td>
															</tr>
													';
												}
									
									
					$table_pdf.='
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>';
						
					$table_pdf.='</table>'; 
					
					if($applet_data!='' || $grid_image_path)
					{    
							$qry="select applet_data, applet_time_interval  from localanesthesiarecord where confirmation_id= $pConfId";
							$qryRes = @imw_query($qry);
							$arrayRes = @imw_fetch_array($qryRes);
							$appletData= $arrayRes['applet_data'];
							$appletTimeInterval= $arrayRes['applet_time_interval'];
							
							$imgNameTime="blank_timeInterval.jpg";
							$fixDateToDisplayOldApplet = '2009-06-14';
							if($confimDOS < $fixDateToDisplayOldApplet) {
								$imgName="bgGrid.jpg";
							}
							else
							{
								$imgName="bgTest.jpg";
							}	
							
							//Applet data 
							$ekgRedLineThikness = 3;
							if($confimDOS < $fixDateToDisplayOldApplet) 
							{
								drawOnImagetime($appletTimeInterval,$imgNameTime,"new_html2pdf/tess_TimeInterval".$y.".jpg");
								drawOnImage2($appletData,$imgName,"new_html2pdf/tess".$y.".jpg",$ekgRedLineThikness);	
								$tessYNew = 'tess'.$y.'.jpg';
								if($savePdfConfirmationId) {
									$tessYNew = 'new_html2pdf/tess'.$y.'.jpg';
								}
								
								$table_pdf.='
									<table width="100%" border="0" valign="top">
										<tr>
											<td width="100%"><img src="'.$tessYNew.'" width="600" height="370"></td>
										</tr>
									</table>
								';
							}
							else 
							{
								/*$filename="new_html2pdf/tess_TimeInterval".$y.".jpg";
								//$new_name=$_REQUEST["thumbimage"];
								$dest_file = "new_html2pdf/tess_TimeInterval".$y.".jpg"; 
								$width=550;
								$height=30;
								$thumb = imagecreatetruecolor(550, 25);
								$source = imagecreatefromjpeg($filename);
								imagecopyresampled($thumb, $source, 0, 0, 0, 0, 550, 30, $width, $height);
								imagejpeg($thumb,$dest_file,"100");
								//imagejpeg($thumb,'',"100");
								$bgForPDFNew = 'bgForPDF.jpg.jpg';
								if($savePdfConfirmationId) {
									$bgForPDFNew = 'new_html2pdf/bgForPDF.jpg';
								}
			
								$meterImage = '<img src="'.$bgForPDFNew.'" height="257" width="38" >';
								$filename="new_html2pdf/tess".$y.".jpg";
								if($blEnableHTMLGrid == true && $grid_image_path){
									$filename=$grid_image_path;	
									$meterImage="";
								}
								//$new_name=$_REQUEST["thumbimage"];
									
								$dest_file = "new_html2pdf/tess".$y.".jpg"; 
								$width=490;
								$height=357;
								$thumb = imagecreatetruecolor(483, 357);
								$source = imagecreatefromjpeg($filename);
									
								imagecopyresampled($thumb, $source, 0, 0, 0, 0, 482, 357, $width, $height);
								imagejpeg($thumb,$dest_file,"100");
								//imagejpeg($thumb,'',"100");*/
								$img_name = create_html_data_image($pConfId, $html_grid_data, $grid_image_path, $startTime);
							}			
					}		
							
					
					
					$table_pdf.='
						<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:480px;vertical-align:top;">
									<table style="width:480px; " cellpadding="0" cellspacing="0">';
									
							$table_pdf.='
							<tr>
								<td colspan="4" style="width:480px;">';
									$tessTimeIntervalImg = 'tess_TimeInterval'.$y.'.jpg';
									$tessImg = 'tess'.$y.'.jpg';
									if($savePdfConfirmationId) {
										$tessTimeIntervalImg = 'new_html2pdf/tess_TimeInterval'.$y.'.jpg';
										$tessImg = 'new_html2pdf/tess'.$y.'.jpg';	
									}
									if($hide_anesthesia_grid=="Yes") {
										//DO NOT SHOW ANESTHESIA GRID	
									}else {
										if(file_exists("new_html2pdf/tess_TimeInterval".$y.".jpg")){
											$table_pdf.="<img src='".$tessTimeIntervalImg."' style='width:480px;'><br>";		
										}
										if(file_exists($img_name)){
											$table_pdf.="<img src='../".$img_name."' style='width:480px;'>";	
										}
									}
							$table_pdf.='
								</td>
							</tr>
						</table>
					</td>			
					<td style="width:260px; border-left:1px solid #C0C0C0;">			
						<table style="width:260px;" cellpadding="0" cellspacing="0">
						<tr>	
							<td colspan="4" style="width:260px;">
								<table style="width:260px;" cellpadding="0" cellspacing="0">	
											<tr>
												<td style="width:250px;" colspan="3" class="bdrbtm">
												<b>1.&nbsp;Routine Monitors Applied:&nbsp;</b>';
												if($routineMonitorApplied!=''){
													$table_pdf.=$routineMonitorApplied;
												}else{$table_pdf.="____";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:250px;" colspan="3" class="bgcolor bold bdrbtm">
													2.&nbsp;IV Catheter
												</td>
											</tr>
											<tr>
												<td style="width:250px;" colspan="3" class="bdrbtm">
													<b>No IV:&nbsp;</b>';
													if($ivCatheter=='Yes'){
														$table_pdf.=$ivCatheter;
													}else{$table_pdf.="____";}
												$table_pdf.='</td>
											</tr>
											<tr>
												<td style="width:80px;" class="bdrbtm bold">Hand:</td>
												<td style="width:80px;" class="bdrbtm">';
												if($hand_right=='Yes'){
													$table_pdf.="<b>Right</b>&nbsp;Yes";
												}else{$table_pdf.="__";}
												$table_pdf.='
												</td>
												<td style="width:80px;" class="bdrbtm">';
												if($hand_left=='Yes'){
													$table_pdf.="<b>Left</b>&nbsp;Yes";
												}else{$table_pdf.="__";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:80px;" class="bdrbtm bold">Wrist:</td>
												<td style="width:80px;" class="bdrbtm">';
												if($wrist_right=='Yes'){
													$table_pdf.="<b>Right</b>&nbsp;Yes";
												}else{$table_pdf.="__";}
												$table_pdf.='
												</td>
												<td style="width:80px;" class="bdrbtm">';
												if($wrist_left=='Yes'){
													$table_pdf.="<b>Left</b>&nbsp;Yes";
												}else{$table_pdf.="__";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:80px;" class="bdrbtm bold">Arm:</td>
												<td style="width:80px;" class="bdrbtm">';
												if($arm_right=='Yes'){
													$table_pdf.="<b>Right</b>&nbsp;Yes";
												}else{$table_pdf.="__";}
												$table_pdf.='
												</td>
												<td style="width:80px;" class="bdrbtm">';
												if($arm_left=='Yes'){
													$table_pdf.="<b>Left</b>&nbsp;Yes";
												}else{$table_pdf.="__";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:80px;" class="bdrbtm bold">Antecubital:</td>
												<td style="width:80px;" class="bdrbtm">';
												if($anti_right=='Yes'){
													$table_pdf.="<b>Right</b>&nbsp;Yes";
												}else{$table_pdf.="__";}
												$table_pdf.='
												</td>
												<td style="width:80px;" class="bdrbtm">';
												if($anti_left=='Yes'){
													$table_pdf.="<b>Left</b>&nbsp;Yes";
												}else{$table_pdf.="__";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:80px;" class="bold bdrbtm">Other:</td>
												<td colspan="2" style="width:160px;" class=" bdrbtm">';
												if($ivCatheterOther!=''){
													$table_pdf.=$ivCatheterOther;
												}else{$table_pdf.="_____";}
												$table_pdf.='	
												</td>
											</tr>
											<tr>
												<td colspan="3" class="bdrbtm bgcolor bold">3.&nbsp;Local Anesthesia</td>
											</tr>
											<tr>
												<td colspan="3" class="bdrbtm">';
												if($TopicalBlock1Block2){
													$table_pdf.="<b>".$TopicalBlock1Block2."</b>:&nbsp;Yes";	
												}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td colspan="3">';
													if($Reblock){
														$table_pdf.='<b>'.$Reblock.'</b>:&nbsp;Yes';
													}
												$table_pdf.='
												</td>	
											</tr>
									</table>
								</td>
							</tr>	
							';
						
						
						
						
				if($TopicalBlock1Block2=='Block1' || $TopicalBlock1Block2=='Block2'){
					$table_pdf.='
					<tr>	
						<td colspan="4" style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm bold"><b>Aspiration:</b>&nbsp;';
						if($Block1Block2Aspiration){
							$table_pdf.=$Block1Block2Aspiration;	
						}else{$table_pdf.="___";}
						$table_pdf.='&nbsp;&nbsp;&nbsp;<b>Full EOM:&nbsp;</b>';
						if($Block1Block2Full=="Yes"){
							$table_pdf.=$Block1Block2Full;	
						}else{$table_pdf.="___";}
						$table_pdf.='
						</td>
					</tr>
					<tr>
						<td colspan="4" style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						<b>Before Injection:&nbsp;</b>';
						if($Block1Block2BeforeInjection=="Yes"){
							$table_pdf.=$Block1Block2BeforeInjection;
						}else{$table_pdf.="___";}
				$table_pdf.='</td>
					</tr>
					<tr>
						<td colspan="4"  style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						<b>Comments:&nbsp;</b>';
						if($Block1Block2Comment){
							$table_pdf.=$Block1Block2Comment;
						}else{$table_pdf.="___";}
						$table_pdf.='</td>
					</tr>';
				}
				$table_pdf.='
				<tr>
					<td colspan="4" style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
					<b>4% lidocaine:&nbsp;</b>';
					if($topical4PercentLidocaine=="Yes"){
						$table_pdf.=$topical4PercentLidocaine;
					}else{$table_pdf.="____";}
					$table_pdf.='</td>
				</tr>';
				$table_pdf.='
				<tr>
					<td style="width:70px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						<b>Intracameral:&nbsp;</b></td>
					<td style="width:30px;" class="bdrbtm">';
						if($Intracameral!=""){
							$table_pdf.=$Intracameral." ml";
						}else{$table_pdf.="____";}
					$table_pdf.='
					</td>
					<td style="width:60px;" class="bold bdrbtm">1%lidocaine MPF:&nbsp;</td>
					<td style="width:30px;" class="bdrbtm">';
						if($Intracameral1percentLidocaine=="Yes"){
							$table_pdf.=$Intracameral1percentLidocaine;
						}else{$table_pdf.="_____";}
						$table_pdf.='
					</td>
				</tr>
				<tr>
					<td style="width:70px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						<b>Peribulbar:&nbsp;</b></td>
					<td style="width:30px;" class="bdrbtm">';
						if($Peribulbar!=""){
							$table_pdf.=$Peribulbar."<br>ml";
						}else{$table_pdf.="____";}
					$table_pdf.='
					</td>
					<td style="width:60px;" class="bold bdrbtm">2% lidocaine:&nbsp;</td>
					<td style="width:30px;" class="bdrbtm">';
						if($Peribulbar2percentLidocaine=="Yes"){
							$table_pdf.=$Peribulbar2percentLidocaine;
						}else{$table_pdf.="_____";}
						$table_pdf.='
					</td>
				</tr>
				<tr>
					<td style="width:70px;" class="bdrbtm">
						Retrobulbar<br><span style="font-size:8px;">Done By Surgeon</span>
						</td>
					<td style="width:30px;" class="bdrbtm">';
						if($Retrobulbar!=""){
							$table_pdf.=$Retrobulbar." mls";
						}else{$table_pdf.="___";}
					$table_pdf.='
					</td>
					<td style="width:60px;" class="bold bdrbtm">3% lidocaine</td>
					<td style="width:30px;" class="bdrbtm">';
						if($Retrobulbar4percentLidocaine=="Yes"){
							$table_pdf.=$Retrobulbar4percentLidocaine;
						}else{$table_pdf.="___";}
						$table_pdf.='
					</td>
				</tr>
				<tr>
					<td colspan="4" style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
					4% lidocaine:';
					if($Hyalauronidase4percentLidocaine=="Yes"){
						$table_pdf.=$Hyalauronidase4percentLidocaine;
					}else{$table_pdf.="___";}
					$table_pdf.=
					'</td>
				</tr>
				<tr>
					<td style="width:70px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						Van Lindt:&nbsp;</td>
					<td style="width:30px;" class="bdrbtm">';
						if($VanLindr!="" || $VanLindrHalfPercentLidocaine=="Yes"){
							$table_pdf.=$VanLind."<br>mls";
						}else{$table_pdf.="____";}
					$table_pdf.='
					</td>
					<td style="width:60px;" class="bold bdrbtm">0.5% Bupivacaine:&nbsp;</td>
					<td style="width:30px;" class="bdrbtm">';
						if($VanLindrHalfPercentLidocaine=="Yes"){
							$table_pdf.=$VanLindrHalfPercentLidocaine;
						}else{$table_pdf.="____";}
						$table_pdf.='
					</td>
				</tr>
				<tr>
					<td colspan="2" style="width:100px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						';
						if($lidTxt!="" || $lidEpi5ug=="Yes"){
							$table_pdf.=$lidTxt." lid ".$lid." mls";
						}else{$table_pdf.="____";}
					$table_pdf.='
					</td>
					<td style="width:60px;" class="bold bdrbtm">Epi 5 ug/ml:&nbsp;</td>
					<td style="width:30px;" class="bdrbtm">';
						if($lidEpi5ug=="Yes"){
							$table_pdf.=$lidEpi5ug;
						}else{$table_pdf.="____";}
						$table_pdf.='
					</td>
				</tr>
				<tr>
					<td colspan="2" style="width:100px;" class="bdrbtm">
						Other:&nbsp;';
						if($otherRegionalAnesthesiaTxt1!='' ||$otherRegionalAnesthesiaDrop!=""){
							$table_pdf.=$otherRegionalAnesthesiaTxt1." ".$otherRegionalAnesthesiaDrop."mls";
						}
					$table_pdf.='
					</td>
					<td style="width:60px;font-size:12px;" class="bold bdrbtm">Wydase 15 u/ml:&nbsp;</td>
					<td style="width:30px;font-size:12px;" class="bdrbtm">';
						if($otherRegionalAnesthesiaWydase15u=="Yes"){
							$table_pdf.=$otherRegionalAnesthesiaWydase15u;
						}else{$table_pdf.="____";}
						$table_pdf.='
					</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm">Other:&nbsp;';
					if($otherRegionalAnesthesiaTxt2!=''){
						$table_pdf.=$otherRegionalAnesthesiaTxt2;
					}else{$table_pdf.="___";}
					$table_pdf.=	
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm bgcolor bold">';
					$table_pdf.='4.&nbsp;Ocular Pressure';
					if($ocular_pressure_na=='Yes'){
						$table_pdf.="&nbsp;&nbsp;&nbsp;&nbsp;N/A:&nbsp;<b>Yes</b>";
					}
					$table_pdf.=	
					'</td>
				</tr>	
				<tr>
					<td colspan="4" class="bdrbtm"><b>None:&nbsp;</b>';
					if($none=='Yes'){
						$table_pdf.=$none;
					}else{$table_pdf.="____";}
					$table_pdf.=
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm"><b>Digital:&nbsp;</b>';
					if($digital=='Yes'){
						$table_pdf.=$digital;
					}else{$table_pdf.="____";}
					$table_pdf.=
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm"><b>Honan Balloon:&nbsp;</b>';
					if($honanballon!='' || $honanBallonAnother!=''){
						$table_pdf.=$honanballon." mm ".$honanBallonAnother." Min";
					}else{$table_pdf.="____";}
					$table_pdf.=
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm"><b>Comment:</b>&nbsp;';
					if($ansComment!=''){
						$table_pdf.=$ansComment;
					}else{$table_pdf.="______";}
					$table_pdf.=
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm">';
						if($signAnesthesia2Status=="Yes"){
							$table_pdf.="<br><b>Anesthesia Provider:&nbsp;</b>".$signAnesthesia2LastName.', '.$signAnesthesia2FirstName;
							$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>Yes";
							$table_pdf.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signAnesthesia2DateTime);
						}else {
							$table_pdf.="<br><b>Anesthesia Provider:&nbsp;</b>________";
							$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>________";
							$table_pdf.="<br><b>Signature Date:&nbsp;</b>________";
						}
						$table_pdf.=
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm"><b>Anesthesia Provider:</b>&nbsp;';
					if($relivedIntraNurseId>0){
						$qrnurse="select lname,fname from users where usersId='$relivedIntraNurseId'";
						$qrRes=imw_query($qrnurse) ;	
						$nursenameres=imw_fetch_array($qrRes);
						$nurseName=	$nursenameres['lname'].',&nbsp;'.$nursenameres['fname'];						
						$table_pdf.=$nurseName;					
					}else{$table_pdf.="_____";}
					$table_pdf.=
					'</td>
				</tr>';
			$table_pdf.=			
			'</table>
		</td>
	</tr>';
							// Vital Sign Grid Printing Section - Start
							
							if($vitalSignGridStatus)
							{
								
								$table_pdf.='<tr>';
								$table_pdf.='<td style="width:700px;" class="bdrbtm" colspan="2" valign="top"><table style="width:700px; " cellpadding="0" cellspacing="0">';
								
								$table_pdf.='<tr>';
								$table_pdf.='<td class="bgcolor bold bdrbtm" colspan="8" style="width:700px; vertical-align:middle; ">&nbsp;&nbsp;Vital Signs</td>';
								$table_pdf.='</tr>';
								
								
								$table_pdf.='<tr>';	
								$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Time</b></td>';
								$table_pdf.='<td colspan="2" class="bdrbtm" style="width:200px; border-right:1px solid #C0C0C0; vertical-align:middle;">&nbsp;<b>B/P</b></td>';
								$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Pulse</b></td>';
								$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>RR</b></td>';
								$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Temp<sup>O</sup> C</b></td>';
								$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>EtCO<sub>2</sub></b></td>';
								$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:80px; vertical-align:middle; ">&nbsp;<b>OSat<sub>2</sub></b></td>';		
								$table_pdf.='</tr>';
								$table_pdf.='<tr>';	
								
								$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Systolic</b></td>';
								$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Diastolic</b></td>';
								
								$table_pdf.='</tr>';
								
								
							$condArr		=	array();
							$condArr['confirmation_id']	=	$pConfId ;
							$condArr['chartName']			=	'mac_regional_anesthesia_form' ;
							
							$gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time','Asc');
							$gCounter		=	0;
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
									
									$table_pdf.='<tr>';	
									$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue1.'</td>';
									$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue2.'</td>';
									$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue3.'</td>';
									$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue4.'</td>';
									$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue5.'</td>';
									$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue6.'</td>';
									$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue7.'</td>';
									$table_pdf.='<td class="bdrbtm" style="width:80px; vertical-align:middle; ">&nbsp;'.$fieldValue8.'</td>';		
									$table_pdf.='</tr>';
										
								}
							}
							
							for($loop = $gCounter; $loop < 3; $loop++)
							{
										$table_pdf.='<tr>';	
										$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
										$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
										$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
										$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
										$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
										$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
										$table_pdf.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
										$table_pdf.='<td class="bdrbtm" style="width:80px; vertical-align:middle; ">&nbsp;</td>';		
										$table_pdf.='</tr>';
								}
								
							$table_pdf.='</table></td>';
							$table_pdf.='</tr>';
							
							
							}
							// Vital Sign Grid Printing Section - End
	
	
	$table_pdf.=	
	'
	<tr>
		<td colspan="2" style="width:700px;">
			<table style="width:700px;" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" style="width:700px;" class="bgcolor bold bdrbtm">Post-Operative</td>
				</tr>
				<tr>
					<td style="width:350px;border-right:1px solid #C0C0C0;:">
						<table style="width:350px;" cellpadding="0" cellspacing="0">
							<tr>	
								<td style="width:350px;" class="bdrbtm">
									<b>No known anesthetic complication:&nbsp;</b>';
									if($anyKnowAnestheticComplication){
										$table_pdf.=$anyKnowAnestheticComplication;
									}else{$table_pdf.="___";}
								$table_pdf.='
								</td>
							</tr>
							<tr>	
								<td style="width:350px;" class="bdrbtm">
									<b>Stable cardiovascular and pulmonary function:&nbsp;</b>';
									if($stableCardiPlumFunction2){
										$table_pdf.=$stableCardiPlumFunction2;
									}else{$table_pdf.="___";}
								$table_pdf.='
								</td>
							</tr>
							<tr>	
								<td style="width:350px;">
									<b>Satisfactory condition for discharge:&nbsp;</b>';
									if($satisfactoryCondition4Discharge){
										$table_pdf.=$satisfactoryCondition4Discharge;
									}else{$table_pdf.="___";}
								$table_pdf.='
								</td>
							</tr>
						</table>
					</td>
					<td style="width:350px;">
						<table style="width:350px;" cellpadding="0" cellspacing="0">
							<tr>	
								<td style="width:80px;" class="bold bdrbtm">Evaluation:</td>
								<td style="width:300px;" class="bdrbtm">';
								if(trim($evaluation)){
									$table_pdf.=htmlentities(stripslashes($evaluation));
								}else{
									$table_pdf.="____";	
								}
								$table_pdf.=
								'</td>
							</tr>
							<tr>	
								<td style="width:80px;" class="bold">Remarks:</td>
								<td style="width:300px;">';
								if(trim($remarks)){
									$table_pdf.=htmlentities(stripslashes($remarks));
								}else{
									$table_pdf.="____";	
								}
								$table_pdf.=
								'</td>
							</tr>
						</table>
					</td>
				</tr>';
		// Start Printing Additional Question
					
			$table_pdf .= $objLocalAnesData->mac_ques_print_html($pConfId);
		
			// End Printing Additional Question				
		$table_pdf.='	
				<tr>
					<td style="width:350px;" class="pl5">';
					if($signAnesthesia3Status=="Yes"){
						$table_pdf.="<b>Anesthesia Provider:&nbsp;</b>".$signAnesthesia3LastName.', '.$signAnesthesia3FirstName;
						$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>Yes";
						$table_pdf.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signAnesthesia3DateTime);
					}else {
						$table_pdf.="<b>Anesthesia Provider:&nbsp;</b>________";
						$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>________";
						$table_pdf.="<br><b>Signature Date:&nbsp;</b>________";
					}
					
					$table_pdf.='
					</td>
					<td style="width:350px;" class="pl5">';
					$userTypeLabel="Anesthesia Provider";
					if($relivedPostNurseId>0){
						$userTypeQry="Anesthesiologist"; 
						$relivedPostQry = "select user_type,lname,fname from users where usersId='".$relivedPostNurseId."' ORDER BY lname";
						$relivedPostRes = imw_query($relivedPostQry) or die(imw_error());
						$userTypeRow 	= imw_fetch_assoc($relivedPostRes);
						$userTypeChk	= $userTypeRow['user_type'];
						
						if($userTypeChk=="Nurse"){
							$userTypeLabel="Nurse";	
							$userTypeQry="Relief Nurse";
						}
						$nurseName2=$userTypeRow['lname'].',&nbsp;'.$userTypeRow['fname'];
					}else {
						$nurseName2 = "________";
					}
					$table_pdf.='<b>'.$userTypeLabel.':&nbsp;</b>'.$nurseName2;
					
					$table_pdf.=
					'</td>
				</tr>
			</table>
		</td>
	</tr>';
				
				
	}
					
				
				
				$table_pdf.='</table>';
				
				$y++;
				$table_pdf.='</page>';		
			
		}	
	}
	
}
//echo $table_pdf;exit();
?>

 <?php 
	if(trim($savePdfConfirmationId) && trim($table_pdf) != "") {
		$content = $table_pdf; 
		$op = 'p';
		$savePdfFileName = $_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/'.$dateFolderPath.'/anes_'.$savePdfPatientName.'_'.$savePdfAscId.'.pdf';
		$html2pdf = new HTML2PDF($op,'A4','en');
		$html2pdf->setTestTdInOnePage(false);
		$html2pdf->WriteHTML($content, isset($_GET['vuehtml']));
		$html2pdf->Output($savePdfFileName,'F');
		
	}else {
		//DO NOTHING 
	}

?>