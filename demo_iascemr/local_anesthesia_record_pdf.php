<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$blEnableHTMLGrid = false;
$headerBar='yes';
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
	

//include("imageSc/imgCreate.php");
include("common_functions.php");
include_once("common/commonFunctions.php");
$tablename = "localanesthesiarecord";
//include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
include_once("library/classes/local_anesthesia.php");
$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {
	$pConfId = $_SESSION['pConfId'];
}	

$objManageData = new manageData;
$objLocalAnesData = new LocalAnesthesia;
extract($_GET);
include_once("new_header_print.php");
$get_http_path=$_REQUEST['get_http_path'];
//

$thisId = $_REQUEST['thisId'];
$innerKey = $_REQUEST['innerKey'];
$preColor = $_REQUEST['preColor'];
//print_r($_GET);
$patient_id = $_REQUEST['patient_id'];
	

//$ascId = $_SESSION['ascId']; 

$cancelRecord = $_REQUEST['cancelRecord'];
$SaveForm_alert = $_REQUEST['SaveForm_alert'];

$slider_row="#CAD8FD";

// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;	
	$surgeonId = $detailConfirmation->surgeonId;
	$anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
	$confimDOS=$detailConfirmation->dos;
	$patient_id=$detailConfirmation->patientId;
// GETTING CONFIRMATION DETAILS
if(!$patient_id) {
	$patient_id = $_SESSION['patient_id'];
}
// GETTING SURGEONS SIGN YES OR NO
	unset($conditionArr);
	$conditionArr['usersId'] = $surgeonId;
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($surgeonsDetails) {
		foreach($surgeonsDetails as $usersDetail){
			$signatureOfSurgeon = $usersDetail->signature;
		}
	}	
// GETTING SURGEONS SIGN YES OR NO

// GETTING SURGEONS SIGN YES OR NO
	unset($conditionArr);
	$conditionArr['usersId'] = $anesthesiologist_id;
	$anesthesiologistDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if($anesthesiologistDetails) {
		foreach($anesthesiologistDetails as $usersDetail){
			$signatureOfAnesthesiologist = $usersDetail->signature;
		}
	}	
// GETTING SURGEONS SIGN YES OR NO
	
// GETTTING PRIMARY AND SECONDARY PROCEDURES
	$procDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
		if($procDetails) {
			extract($procDetails);
		}	
// GETTTING PRIMARY AND SECONDARY PROCEDURES


$submitMe = $_REQUEST['submitMe'];

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
 $update_status=imw_query("update stub_tbl set patient_status='IOA' where stub_id='$stub_id'");
}
//END UPDATING PATIENT STATUS IN STUB TABLE

?>

<div id="post" style="display:none;"></div>
<?php
$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'localanesthesiarecord' AND patient_conf_id = '$pConfId' ";
$rsNotes =imw_query($query_rsNotes);
$totalRows_rsNotes =imw_num_rows($rsNotes);

//include("common/pre_defined_popup.php");
//FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION
	function calculate_timeFun($MainTime) {
		global $objManageData;
		$MainTime		=	$objManageData->getTmFormat($MainTime);
		return $MainTime;
		/*
		$time_split = explode(":",$MainTime);
		if($time_split[0]=='24') { //to correct previously saved records
			$MainTime = "12".":".$time_split[1].":".$time_split[2];
		}
		if($MainTime == "00:00:00") {
			$MainTime = "";
		}else {
			//$MainTime = date('h:iA',strtotime($MainTime));
			
			$MainTime		=	$objManageData->getTmFormat($MainTime);
			$MainTime = substr($MainTime,0,-1);
		}
		*/
		
	}	
//END FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION

	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$patient_primary_procedure = $detailConfirmationFinalize->patient_primary_procedure;
	$patient_secondary_procedure = $detailConfirmationFinalize->patient_secondary_procedure;
	$site = $detailConfirmationFinalize->site;
	
	$settings = $objManageData->loadSettings('asa_4,anes_mallampetti_score');	

//GET DETAIL FROM PRE-OP NURSE RECORD
	$getPreOpNursingDetails = $objManageData->getRowRecord('preopnursingrecord', 'confirmation_id', $pConfId);
//GET DETAIL FROM PRE-OP NURSE RECORD

// GETTTING LOCAL ANES RECORD IF EXISTS
	$localAnesRecordDetails = $objManageData->getExtractRecord('localanesthesiarecord', 'confirmation_id', $pConfId, " *, date_format(signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia1DateTimeFormat , date_format(signAnesthesia2DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia2DateTimeFormat , date_format(signAnesthesia3DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia3DateTimeFormat, date_format(signAnesthesia4DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia4DateTimeFormat ");

		$localanesFormStatus='';
		if($localAnesRecordDetails){
			$localAnesRecordDetails = array_map('stripslashes',$localAnesRecordDetails);
			extract($localAnesRecordDetails);
			//list($bp1, $bp2) = explode(", ", $bp);
			
			$orStartTime=calculate_timeFun($orStartTime); //CODE TO DISPLAY OR START TIME
			$orStopTime=calculate_timeFun($orStopTime); //CODE TO DISPLAY OR STOP TIME
			$startTime=calculate_timeFun($startTime); //CODE TO DISPLAY START TIME
			$stopTime=calculate_timeFun($stopTime); //CODE TO DISPLAY STOP TIME
			$newStartTime2=calculate_timeFun($newStartTime2); //CODE TO DISPLAY New START TIME 2
			$newStopTime2=calculate_timeFun($newStopTime2); //CODE TO DISPLAY New STOP TIME 2
			$newStartTime3=calculate_timeFun($newStartTime3); //CODE TO DISPLAY New START TIME 3
			$newStopTime3=calculate_timeFun($newStopTime3); //CODE TO DISPLAY New STOP TIME 3
			
			/*
			$orStartTime = $objManageData->getTmFormat($orStartTime);
			$orStopTime = $objManageData->getTmFormat($orStopTime);
			$startTime = $objManageData->getTmFormat($startTime);
			$stopTime = $objManageData->getTmFormat($stopTime);
			$newStartTime2 = $objManageData->getTmFormat($newStartTime2);
			$newStopTime2 = $objManageData->getTmFormat($newStopTime2);
			$newStartTime3 = $objManageData->getTmFormat($newStartTime3);
			$newStopTime3 = $objManageData->getTmFormat($newStopTime3);
			*/
			$localanesFormStatus = $form_status;
			$localAnesRecordDetailsMedGrid = $objManageData->getExtractRecord('localanesthesiarecordmedgrid', 'confirmation_id', $pConfId);
			if($localAnesRecordDetailsMedGrid){
				$localAnesRecordDetailsMedGrid = array_map('stripslashes',$localAnesRecordDetailsMedGrid);
				extract($localAnesRecordDetailsMedGrid);
			}
			$localAnesRecordDetailsMedGridSec = $objManageData->getExtractRecord('localanesthesiarecordmedgridsec', 'confirmation_id', $pConfId);
			if($localAnesRecordDetailsMedGridSec){
				$localAnesRecordDetailsMedGridSec = array_map('stripslashes',$localAnesRecordDetailsMedGridSec);
				extract($localAnesRecordDetailsMedGridSec);
			}
			
			if($anes_ScanUploadPath || $anes_ScanUpload){
				$scnImgSrc = 'new_html2pdf/anesScanUpld'.$localAnesthesiaRecordId.'pg.jpg';
				$newImages='';
				if($anes_ScanUploadType == 'application/pdf') {
					$countPdfPages =  $objManageData->getNumPagesPdf($anes_ScanUploadPath);
					$path = realpath(dirname(__FILE__));
					exec("convert ".$anes_ScanUploadPath." new_html2pdf/anesScanUpld".$localAnesthesiaRecordId."pg%d.jpg");
					for($k=0; $k<$countPdfPages;$k++) {
						$newSize=' width="620" height="650"';
						$scnImgPdfSrc="anesScanUpld".$localAnesthesiaRecordId."pg".$k.".jpg";						
						$newImages.='
						<tr >
							<td width="100%"><img style="border:none; cursor:pointer;"  src="'.$scnImgPdfSrc.'"   '.$newSize.' ></td>
						</tr>';
					}
					if(!$anes_ScanUploadPath && $anes_ScanUpload){//CODE TO SHOW OLD SAVED RECORDS
						$scnImgSrc = "admin/logoImg.php?from=local_anesthesia_record&id=".$localAnesthesiaRecordId;	
						$newImages.='
						<tr >
							<td width="100%"><img style="border:none; cursor:pointer;"  src="'.$scnImgSrc.'" ></td>
						</tr>';
					}
				}else {
					if(!$anes_ScanUploadPath && $anes_ScanUpload){//CODE TO SHOW OLD SAVED RECORDS
						$bakImgResource = imagecreatefromstring($anes_ScanUpload);
						imagejpeg($bakImgResource,$scnImgSrc);
						$file=fopen($scnImgSrc,'w+');
						fputs($file,$anes_ScanUpload);
					}else if($anes_ScanUploadPath) {
						copy($anes_ScanUploadPath,$scnImgSrc);
					}
					$newSize=' width="150" height="100"';
					$priImageSize=array();
					if(file_exists($scnImgSrc)) {
						$priImageSize = getimagesize($scnImgSrc);
						if($priImageSize[0] > 395 && $priImageSize[1] < 840){
							$newSize = $objManageData->imageResize(680,400,710);						
							$priImageSize[0] = 710;
						}
											
						elseif($priImageSize[1] > 840){
							$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],700);						
							$priImageSize[1] = 700;
						}
						else{					
							$newSize = $priImageSize[3];
						}							
						if($priImageSize[1] > 800 ){					
							echo '<newpage>';
						}
					}
					$scnImgPdfSrc = 'anesScanUpld'.$localAnesthesiaRecordId.'pg.jpg';
						$newImages.='
						<tr >
							<td width="100%"><img style="border:none; cursor:pointer;"  src="'.$scnImgPdfSrc.'"   '.$newSize.'></td>
						</tr>';
					
				}
			}
			
			//CODE TO SET $apTime 
				if($apTime=="00:00:00" || $apTime=="") {
					
				$apTime="";
				}else {
					
					$apTime=$apTime;
					
					$time_split_apTime = explode(":",$apTime);
					if($time_split_apTime[0]>=12) {
						$am_pm = "PM";
					}else {
						$am_pm = "AM";
					}
					if($time_split_apTime[0]>=13) {
						$time_split_apTime[0] = $time_split_apTime[0]-12;
						if(strlen($time_split_apTime[0]) == 1) {
							$time_split_apTime[0] = "0".$time_split_apTime[0];
						}
					}else {
						//DO NOTHNING
					}
					//echo $time_split_apTime[1];
					$apTime = $time_split_apTime[0].":".$time_split_apTime[1]." ".$am_pm;
				}
			//END CODE TO SET apTime
			
		}
// GETTTING LOCAL ANES RECORD IF EXISTS

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
if($headerBar=='yes') {$table_print.='<page backtop="5" >';}
$table_print.=$head_table;
$table_print.='<table style="width:744px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:744px;" class="fheader">MAC/Local/Regional Anesthesia Record</td>
				</tr>';
				if($anes_ScanUploadPath || $anes_ScanUpload){
					$table_print.='
					<tr>	
						<td style="width:700px;text-align:center;">'.$newImages.'</td>
					</tr>';
				}else{
					
					if($version_num > 1) 
					{
					$table_print.='
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
										$table_print.='<b>Anethesia Provider:</b> '.$signAnesthesia4LastName.", ".$signAnesthesia4FirstName." ".$signAnesthesia4MiddleName."<br>
											<b>Electronically Signed:&nbsp;</b>Yes"; 
									}else {
										$table_print.='__________';
									}	
								
					$table_print.='
									</td>
									<td style="width:235px;vertical-align:top;text-align:right; white-space:nowrap">';
									$getReliefNurseName = "";
					$table_print.="<b>Relief Nurse / Anesthesia:</b><br>";
									if($reliefNurseId){
										$getReliefNurseAnesName = getUsrNm($reliefNurseId);
										$table_print.=$getReliefNurseAnesName[0];   
									}else{
										$table_print.='_________';	
									}	
					$table_print.='
									</td>
								</tr>';
					if($signAnesthesia4Id)
					{
					$table_print.='	
								<tr>
									<td>&nbsp;</td>
									<td colspan="2">
										<b>Signature Date:&nbsp;</b>' .$objManageData->getFullDtTmFormat($signAnesthesia4DateTime).'
									</td>
								</tr>';
					}
					$table_print.='
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
									if($confirmIPPSC_signin) { $table_print.=stripslashes($confirmIPPSC_signin); }else{$table_print.="___"; }
									$table_print.='</td>
									<td class="bdrbtm" style="text-align:left;width:320px; ">Site marked by person performing the procedure</td>
									<td class="bdrbtm cbold" style="width:50px;"> ';
									if($siteMarked) { $table_print.=stripslashes($siteMarked); }else{$table_print.="___"; }
									$table_print.='</td>
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
									if($patientAllergies) { $table_print.=stripslashes($patientAllergies); }else{$table_print.="___"; }
									$table_print.='</td>
									<td class="bdrbtm" style="text-align:left;width:320px; ">Difficult airway or aspiration risk?</td>
									<td class="bdrbtm cbold" style="width:50px;"> ';
									if($difficultAirway) { $table_print.=stripslashes($difficultAirway); }else{$table_print.="___"; }
									$table_print.='</td>
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
														$table_print.='<b># of units available:&nbsp;</b>';
														$table_print.=htmlentities(stripslashes($bloodLossUnits)); 
														if(!$bloodLossUnits){$table_print.="____";}
													}else{ $table_print.="&nbsp;"; }
													
												$table_print.='
												</td>
											</tr>
										</table>
									</td>
									<td class="bdrbtm cbold" style="text-align:center; width:50px; style="border-right:1px solid #C0C0C0;"">';
									 if($riskBloodLoss) { $table_print.=stripslashes($riskBloodLoss);}else{$table_print.="__"; }
									$table_print.='</td>
									
									
									<td class="bdrbtm" style="text-align:left;width:320px; ">Anesthesia safety check completed</td>
									<td class="bdrbtm cbold" style="width:50px;"> ';
									if($anesthesiaSafety) { $table_print.=stripslashes($anesthesiaSafety); }else{$table_print.="___"; }
									$table_print.='</td>
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
									if($allMembersTeam) { $table_print.=stripslashes($allMembersTeam); }else{$table_print.="___"; }
									$table_print.='</td>
									<td class="bdrbtm" style="width:320px;">&nbsp;</td>
									<td class="bdrbtm cbold" style="text-align:center; width:50px; style="border-right:1px solid #C0C0C0;"">';
									$table_print.='&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
					';
					}
					
					
					$table_print.='
					<tr>
						<td style="width:744px;" class="bold bdrtop bgcolor">Pre-Operative</td>
					</tr>
					<tr>	
						<td style="width:740px;" class="bdrtop">
							<table style="width:740px;;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:170px; text-align:left" class="bdrbtm"><b>Patient Interviewed:</b>
									';
									if($patientInterviewed=="Yes"){
										$table_print.="Done";
									}else{$table_print.="___";}
									$table_print.='
									</td>
									<td style="width:170px;" class="bdrbtm"><b>No change in H&amp;P:</b>&nbsp;';
									if($chartNotesReviewed=="Yes"){
										$table_print.="Done";
									}else{$table_print.="___";}
									$table_print.='
									</td>
									<td style="width:180px" class="bdrbtm">';
									if($version_num > 5) {
										$table_print.='
										<b>Changes in H&amp;P documented:</b>&nbsp;';
										if($chartNotesReviewed=='Changed'){
											$table_print.="Done";
										}else{$table_print.="___";}
									}
									$table_print.='
									</td>
									<td style="width:200px;" class="bdrbtm ">&nbsp;<b>Alert and Awake:</b>&nbsp;';
									if(trim($alertOriented)){
										$table_print.=trim($alert_oriented_name);
									}else{$table_print.="___";}
									$table_print.='
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="width:740px;">
							<table style="width:740px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:130px;" class="bdrbtm bold">Procedure Verified:</td>
									<td style="width:200px;" class="bdrbtm">';
									if($patient_primary_procedure){	
										$table_print.=wordwrap($patient_primary_procedure,40,"<br>",1);
									}else{$table_print.="________";}
								$table_print.=
									'</td>
									<td style="width:130px;" class="bdrbtm bold">Secondary Verified:</td>
									<td style="width:200px;" class="bdrbtm">';
									if($procedureSecondaryVerified){	
										$table_print.=wordwrap($patient_secondary_procedure,40,"<br>",1);
									}else{$table_print.="________";}
								$table_print.=
									'</td>
								</tr>
								<tr>
									<td colspan="2" class="bdrbtm"><b>Site Verified '.$siteShow.':&nbsp;</b>';
									if($siteVerified!=''){
										$table_print.=$siteVerified; 
									}else{$table_print.="_____";}
									if($version_num > 2) {
										$table_print.="&nbsp;&nbsp;&nbsp;<b>NPO:&nbsp;</b>";
										if($npo!=''){
											$table_print.="Done";
										}else{$table_print.="_____";}	
									}
									$table_print.=
									'</td>
									<td colspan="2" class="bdrbtm"><b>Assisted by Translator:&nbsp;</b>';
									if($assistedByTranslator=='yes'){
										$table_print.="Done";
									}else{$table_print.="_____";}
									if($version_num > 2 && ($settings['anes_mallampetti_score'] || trim($mallampetti_score))) {
										$table_print.="&nbsp;&nbsp;&nbsp;<b>Mallampetti Score:&nbsp;</b>";
										if($mallampetti_score!=''){
											$table_print.=$mallampetti_score;
										}else{$table_print.="_____";}	
									}
									$table_print.=
									'</td>
								</tr>
								<tr>
									<td colspan="2" style="width:350px;" class="bdrbtm"><b>Pt reassessed, stable<br>for anesthesia / surgery:</b>&nbsp;';
									if($fpExamPerformed=='Yes'){
										$table_print.="Done";
									}else{$table_print.="___";}
									$table_print.='
									</td>
									<td colspan="2" style="width:350px;" class="bdrbtm">';
									if($version_num > 4) {
										$ivSelection 		= ucfirst($getPreOpNursingDetails->ivSelection);
										$ivSelectionOther 	= stripslashes($getPreOpNursingDetails->ivSelectionOther);
										$ivSelectionVal 	= (strtolower($ivSelection)=='other') ? $ivSelectionOther : $ivSelection;
										
										$ivSelectionSide 	= ucfirst($getPreOpNursingDetails->ivSelectionSide);
										
										$gauge 				= $getPreOpNursingDetails->gauge;
										$gauge_other 		= $getPreOpNursingDetails->gauge_other;
										$gaugeVal 			= (strtolower($gauge)=='other') ? $gauge_other : $gauge;
										
										$table_print.='<b>IV:&nbsp;</b>';
										if($ivSelectionVal){
											$table_print.=$ivSelectionVal;
										}else{$table_print.="_____";}
										if(strtolower($ivSelection)!='other') {
											$table_print.="&nbsp;&nbsp;&nbsp;<b>Right/Left:&nbsp;</b>";
											if($ivSelectionSide && $ivSelection){
												$table_print.=$ivSelectionSide;
											}else{$table_print.="_____";}
											$table_print.="&nbsp;&nbsp;&nbsp;<b>Gauge:&nbsp;</b>";
											if($gaugeVal && $ivSelection){
												$table_print.=$gaugeVal;
											}else{$table_print.="_____";}	
										}
									}
									$table_print.=
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
											</tr>';
												$allrgiesQry=("select allergy_name,reaction_name from patient_allergies_tbl where patient_confirmation_id=$pConfId");
												$allergiesRes = imw_query($allrgiesQry);
												$allergiesnum=@imw_num_rows($allergiesRes);
											if($allergiesnum>0){
												while($detailsAllergy=@imw_fetch_array($allergiesRes)){
													$table_print.='
													<tr>
														<td style="width:200px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($detailsAllergy['allergy_name'])).'</td>
														<td style="width:140px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($detailsAllergy['reaction_name'])).'</td>
													</tr>';
												}				
											}else{
											$table_print.='
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
									$table_print.='
										</table>
									</td>
									<td colspan="2" style="width:350px;vertical-align:top;">
										<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
													<tr>
														<td style="width:125px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Name</td>
														<td style="width:100px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Dosage</td>
														<td style="width:100px;font-weight:bold;padding-top:5px;border-bottom:1px solid #C0C0C0;">Sig</td>
													</tr>';
											$medsQry=("select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_anesthesia_medication_tbl where confirmation_id=$pConfId ORDER BY prescription_medication_name");
											$medsRes = imw_query($medsQry);
											$medsnum=@imw_num_rows($medsRes);
											if($medsnum>0){
												while($detailsMeds=@imw_fetch_array($medsRes)){
												$table_print.='
													<tr>
														<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_name']).'</td>
														<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_desc']).'</td>
														<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid">'.htmlentities($detailsMeds['prescription_medication_sig']).'</td>
													</tr>';														
												}
											}else {
												for($q=1;$q<=3;$q++) {
													$table_print.='
													<tr>
														<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid">&nbsp;</td>
														<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid">&nbsp;</td>
														<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid">&nbsp;</td>
													</tr>';
												}
													
											}
									$table_print.='		
										</table>
									</td>
								</tr>	
							</table>
						</td>
					</tr>
					<tr>
						<td style="width:700px;" >
							<table style="width:700px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:120px;" class="bdrbtm"><b>Time:&nbsp;</b>';
									if($bp_p_rr_time <> '00:00:00' && !empty($bp_p_rr_time)){
										//$bp_p_rr_time=date('h:i A',strtotime($bp_p_rr_time));
										$bp_p_rr_time		=	$objManageData->getTmFormat($bp_p_rr_time);
									}else{
										$bp_p_rr_time	=	'';	
									}
									
									if($bp_p_rr_time){
										$table_print.=$bp_p_rr_time;
									}else{$table_print.="___";}
									$table_print.=
									'</td>
									<td style="width:70px;" class="bdrbtm"><b>BP:&nbsp;</b>';
									if($bp){
										$table_print.=$bp;
									}else{$table_print.="___";}
									$table_print.=
									'</td>
									<td style="width:70px;" class="bdrbtm"><b>P:&nbsp;</b>';
									if($P){
										$table_print.=$P;
									}else{$table_print.="___";}
									$table_print.=
									'</td>
									<td style="width:70px;" class="bdrbtm"><b>RR:&nbsp;</b>';
									if($rr){
										$table_print.=$rr;
									}else{$table_print.="___";}
									$table_print.=
									'</td>
									<td style="width:70px;" class="bdrbtm"><b>SaO<sub>2</sub>:&nbsp;</b>';
									if($sao){
										$table_print.=$sao;
									}else{$table_print.="___";}
									$table_print.=
									'</td>
									<td colspan="2" style="width:300px;" class="bold bdrbtm">&nbsp;</td>
								</tr>
								<tr>
									<td style="width:120px;" class="bold bdrbtm">Evaluation:&nbsp;';
									$table_print.=
									'</td>
									<td colspan="4" style="width:280px;" class="bdrbtm">';
									if(trim($evaluation2)){
										$table_print.=htmlentities(stripslashes($evaluation2));
									}else{$table_print.="_____";}
									$table_print.='
									</td>';
									$table_print.='
									<td style="width:80px;" class="bold bdrbtm">';
									if($version_num > 2) {
										$table_print.='Dentition:&nbsp;';
									}
									$table_print.=
									'</td>
									<td style="width:220px;" class="bdrbtm">';
									if($version_num > 2) {
										if(trim($dentation)){
											$table_print.=htmlentities(stripslashes($dentation));
										}else{$table_print.="_____";}
									}
									$table_print.=
									'</td>';
									
									$table_print.='
								</tr>
								<tr>
									<td colspan="5" style="width:400px;" class="bdrbtm">
										<b>Stable cardiovascular and Pulmonary function:&nbsp;</b>';
									if(trim($stableCardiPlumFunction)){
										$table_print.="Done";
									}else{$table_print.="_____";}	
									$table_print.=
									'</td>
									<td colspan="2" style="width:315px;" class="bdrbtm">
										<b>Blood Sugar:&nbsp;</b>';
										if($NA!='' || $bsValue!=''){
											if($NA=='1') { $bsValue='NA'; }
											$table_print.=$bsValue;
										}else{$table_print.="_____";}	
									$table_print.=	
									'</td>
								</tr>
								<tr>
									<td  colspan="7" class="bdrbtm" style="font-size:13px;width:700px;">
										<b>Plan regional anesthesia with sedation.Risks,benefits and alternatives of anesthesia plan have been discussed:</b>';
									if($planAnesthesia!=''){
										$table_print.="Done";
									}else{$table_print.="____";}
									$table_print.=
									'</td>
								</tr>
								<tr>
									<td  colspan="5" style="width:400px;" class="bdrbtm">
										<b>All questions answered:&nbsp;</b>';
										if($allQuesAnswered){
											$table_print.=$allQuesAnswered;
										}else{$table_print.="____";}
										$val="___";
										if($asaPhysicalStatus!=''){
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
									$table_print.="&nbsp;&nbsp;&nbsp;<b>ASA Physical Status:&nbsp;</b>".$val;
									$table_print.=	
									'</td>
									<td style="width:300px;" colspan="2" class="bdrbtm">';
									if($signAnesthesia1Status=="Yes"){
										$table_print.="<br><b>Anesthesia Provider:&nbsp;</b>".$signAnesthesia1LastName.', '.$signAnesthesia1FirstName;
										$table_print.="<br><b>Electronically Signed:&nbsp;</b>Yes";
										$table_print.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signAnesthesia1DateTime);
									}else {
										$table_print.="<br><b>Anesthesia Provider:&nbsp;</b>________";
										$table_print.="<br><b>Electronically Signed:&nbsp;</b>________";
										$table_print.="<br><b>Signature Date:&nbsp;</b>________";
									}
									$table_print.=	
									'</td>
								</tr>
							</table>
						</td>
					</tr>
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
							//$time		=	($time)	?	date('h:iA', strtotime($time))	:	''	;
							$time		=	$objManageData->getTmFormat($time);
							$$t_var		=	$date.' ' .$time;
							
							//echo $var.' : '.$$var.' * ' .$t_var .' : '.$$t_var .'<br>';
						}
					}
					
					if(		$propofol_1!=''  || $propofol_2!=''  || $propofol_3!='' 
							||$propofol_4!=''  || $propofol_5!=''  || $propofol_6!='' 
							||$propofol_7!=''  || $propofol_8!=''  || $propofol_9!='' 
							||$propofol_10!='' || $propofol_11!='' || $propofol_12!='' 
							||$propofol_13!='' || $propofol_14!='' || $propofol_15!='' 
							||$propofol_16!='' || $propofol_17!='' || $propofol_18!=''
							||$propofol_19!='' || $propofol_20!='' ){
							  $propArray=array($propofol_1,$propofol_2,$propofol_3,
							  					$propofol_4,$propofol_5,$propofol_6,
												$propofol_7,$propofol_8,$propofol_9,
												$propofol_10,$propofol_11,$propofol_12,$propofol_13,
							  					$propofol_14,$propofol_15,$propofol_16,
												$propofol_17,$propofol_18,$propofol_19,
												$propofol_20);
								$t_propArray=array($t_propofol_1,$t_propofol_2,$t_propofol_3,
							  					$t_propofol_4,$t_propofol_5,$t_propofol_6,
												$t_propofol_7,$t_propofol_8,$t_propofol_9,
												$t_propofol_10,$t_propofol_11,$t_propofol_12,$t_propofol_13,
							  					$t_propofol_14,$t_propofol_15,$t_propofol_16,
												$t_propofol_17,$t_propofol_18,$t_propofol_19,
												$t_propofol_20);				
												
							}
							
							if(		$midazolam_1!=''  || $midazolam_2!=''  || $midazolam_3!=''
									||$midazolam_4!=''  || $midazolam_5!=''  || $midazolam_6!=''
									||$midazolam_7!=''  || $midazolam_8!=''  || $midazolam_9!='' 
									||$midazolam_10!='' || $midazolam_11!='' || $midazolam_12!=''
									||$midazolam_13!='' || $midazolam_14!='' || $midazolam_15!=''
									||$midazolam_16!='' || $midazolam_17!='' || $midazolam_18!=''
									||$midazolam_19!='' || $midazolam_20!='' ){
							 $midArray=array($midazolam_1,$midazolam_2,$midazolam_3,
							 				$midazolam_4,$midazolam_5,$midazolam_6,
											$midazolam_7,$midazolam_8,$midazolam_9,
											$midazolam_10,$midazolam_11,$midazolam_12,$midazolam_13,
							 				$midazolam_14,$midazolam_15,$midazolam_16,
											$midazolam_17,$midazolam_18,$midazolam_19,
											$midazolam_20);	
							$t_midArray=array($t_midazolam_1,$t_midazolam_2,$t_midazolam_3,
							 				$t_midazolam_4,$t_midazolam_5,$t_midazolam_6,
											$t_midazolam_7,$t_midazolam_8,$t_midazolam_9,
											$t_midazolam_10,$t_midazolam_11,$t_midazolam_12,$t_midazolam_13,
							 				$t_midazolam_14,$t_midazolam_15,$t_midazolam_16,
											$t_midazolam_17,$t_midazolam_18,$t_midazolam_19,
											$t_midazolam_20);						
							}
							
								if(		$Fentanyl_1!=''  || $Fentanyl_2!=''  || $Fentanyl_3!=''
										||$Fentanyl_4!=''  || $Fentanyl_5!=''  || $Fentanyl_6!=''
										||$Fentanyl_7!=''  || $Fentanyl_8!=''  || $Fentanyl_9!=''
										||$Fentanyl_10!='' || $Fentanyl_11!='' || $Fentanyl_12!=''
										||$Fentanyl_13!='' || $Fentanyl_14!='' || $Fentanyl_15!=''
										||$Fentanyl_16!='' || $Fentanyl_17!='' || $Fentanyl_18!=''
										||$Fentanyl_19!='' || $Fentanyl_20!=''  ){
							  $FentanylArray=array($Fentanyl_1,$Fentanyl_2,$Fentanyl_3,
							  						$Fentanyl_4,$Fentanyl_5,$Fentanyl_6,
													$Fentanyl_7,$Fentanyl_8,$Fentanyl_9,
													$Fentanyl_10,$Fentanyl_11,$Fentanyl_12,$Fentanyl_13,
							  						$Fentanyl_14,$Fentanyl_15,$Fentanyl_16,
													$Fentanyl_17,$Fentanyl_18,$Fentanyl_19,
													$Fentanyl_20);
								$t_FentanylArray=array($t_Fentanyl_1,$t_Fentanyl_2,$t_Fentanyl_3,
							  						$t_Fentanyl_4,$t_Fentanyl_5,$t_Fentanyl_6,
													$t_Fentanyl_7,$t_Fentanyl_8,$t_Fentanyl_9,
													$t_Fentanyl_10,$t_Fentanyl_11,$t_Fentanyl_12,$t_Fentanyl_13,
							  						$t_Fentanyl_14,$t_Fentanyl_15,$t_Fentanyl_16,
													$t_Fentanyl_17,$t_Fentanyl_18,$t_Fentanyl_19,
													$t_Fentanyl_20);						
							}
							if(		$ketamine_1!=''  || $ketamine_2!=''  || $ketamine_3!='' 
									||$ketamine_4!=''  || $ketamine_5!=''  || $ketamine_6!=''
									||$ketamine_7!=''  || $ketamine_8!=''  || $ketamine_9!=''
									||$ketamine_10!='' || $ketamine_11!='' || $ketamine_12!=''
									||$ketamine_13!='' || $ketamine_14!='' || $ketamine_15!=''
									||$ketamine_16!='' || $ketamine_17!='' || $ketamine_18!=''
									||$ketamine_19!='' || $ketamine_20!='' ){
							  $ketaArray=array($ketamine_1,$ketamine_2,$ketamine_3,
							  					$ketamine_4,$ketamine_5,$ketamine_6,
												$ketamine_7,$ketamine_8,$ketamine_9,
												$ketamine_10,$ketamine_11,$ketamine_12,$ketamine_13,
							  					$ketamine_14,$ketamine_15,$ketamine_16,
												$ketamine_17,$ketamine_18,$ketamine_19,
												$ketamine_20);	
								$t_ketaArray=array($t_ketamine_1,$t_ketamine_2,$t_ketamine_3,
							  					$t_ketamine_4,$t_ketamine_5,$t_ketamine_6,
												$t_ketamine_7,$t_ketamine_8,$t_ketamine_9,
												$t_ketamine_10,$t_ketamine_11,$t_ketamine_12,$t_ketamine_13,
							  					$t_ketamine_14,$t_ketamine_15,$t_ketamine_16,
												$t_ketamine_17,$t_ketamine_18,$t_ketamine_19,
												$t_ketamine_20);					
							}
							if(		$labetalol_1!=''  || $labetalol_2!=''  || $labetalol_3!=''
									||$labetalol_4!=''  || $labetalol_5!=''  || $labetalol_6!='' 
									||$labetalol_7!=''  || $labetalol_8!=''  || $labetalol_9!='' 
									||$labetalol_10!='' || $labetalol_11!='' || $labetalol_12!=''
									||$labetalol_13!='' || $labetalol_14!='' || $labetalol_15!=''
									||$labetalol_16!='' || $labetalol_17!='' || $labetalol_18!=''
									||$labetalol_19!='' || $labetalol_20!='' ){
							  $labeArray=array($labetalol_1,$labetalol_2,$labetalol_3,
							  					$labetalol_4,$labetalol_5,$labetalol_6,
												$labetalol_7,$labetalol_8,$labetalol_9,
												$labetalol_10,$labetalol_11,$labetalol_12,$labetalol_13,
							  					$labetalol_14,$labetalol_15,$labetalol_16,
												$labetalol_17,$labetalol_18,$labetalol_19,
												$labetalol_20);
								$t_labeArray=array($t_labetalol_1,$t_labetalol_2,$t_labetalol_3,
							  					$t_labetalol_4,$t_labetalol_5,$t_labetalol_6,
												$t_labetalol_7,$t_labetalol_8,$t_labetalol_9,
												$t_labetalol_10,$t_labetalol_11,$t_labetalol_12,$t_labetalol_13,
							  					$t_labetalol_14,$t_labetalol_15,$t_labetalol_16,
												$t_labetalol_17,$t_labetalol_18,$t_labetalol_19,
												$t_labetalol_20);				
							}
							if(	  $spo2_1!=''  || $spo2_2!=''  || $spo2_3!=''  || $spo2_4!=''
								 || $spo2_5!=''  || $spo2_6!=''  || $spo2_7!=''  || $spo2_8!=''
								 || $spo2_9!=''  || $spo2_10!='' || $spo2_11!='' || $spo2_12!=''
								 || $spo2_13!='' || $spo2_14!='' || $spo2_15!='' || $spo2_16!=''
								 || $spo2_17!='' || $spo2_18!='' || $spo2_19!='' || $spo2_20!=''  ){
							  $spoArray=array($spo2_1,$spo2_2,$spo2_3,$spo2_4,$spo2_5,$spo2_6,
							  					$spo2_7,$spo2_8,$spo2_9,$spo2_10,$spo2_11,$spo2_12,
												$spo2_13,$spo2_14,$spo2_15,$spo2_16,
							  					$spo2_17,$spo2_18,$spo2_19,$spo2_20);
								$t_spoArray=array($t_spo2_1,$t_spo2_2,$t_spo2_3,$t_spo2_4,$t_spo2_5,$t_spo2_6,
							  					$t_spo2_7,$t_spo2_8,$t_spo2_9,$t_spo2_10,$t_spo2_11,$t_spo2_12,
												$t_spo2_13,$t_spo2_14,$t_spo2_15,$t_spo2_16,
							  					$t_spo2_17,$t_spo2_18,$t_spo2_19,$t_spo2_20);				
							 }
							 if(	$blank1_1!=''  || $blank1_2!=''  || $blank1_3!=''  || $blank1_4!=''
								 || $blank1_5!=''  || $blank1_6!=''  || $blank1_7!=''  || $blank1_8!=''
								 || $blank1_9!=''  || $blank1_10!='' || $blank1_11!='' || $blank1_12!=''
								 || $blank1_13!='' || $blank1_14!='' || $blank1_15!='' || $blank1_16!=''
								 || $blank1_17!='' || $blank1_18!='' || $blank1_19!='' || $blank1_20!='' ){
							  $blank1Array=array($blank1_1,$blank1_2,$blank1_3,$blank1_4,$blank1_5,$blank1_6,
							  					$blank1_7,$blank1_8,$blank1_9,$blank1_10,$blank1_11,$blank1_12,
												$blank1_13,$blank1_14,$blank1_15,$blank1_16,
							  					$blank1_17,$blank1_18,$blank1_19,$blank1_20);
								$t_blank1Array=array($t_blank1_1,$t_blank1_2,$t_blank1_3,$t_blank1_4,$t_blank1_5,$t_blank1_6,
							  					$t_blank1_7,$t_blank1_8,$t_blank1_9,$t_blank1_10,$t_blank1_11,$t_blank1_12,
												$t_blank1_13,$t_blank1_14,$t_blank1_15,$t_blank1_16,
							  					$t_blank1_17,$t_blank1_18,$t_blank1_19,$t_blank1_20);				
							 }
							 if(		$blank2_1!=''  || $blank2_2!=''  || $blank2_3!=''  || $blank2_4!=''
									 || $blank2_5!=''  || $blank2_6!=''  || $blank2_7!=''  || $blank2_8!=''
									 || $blank2_9!=''  || $blank2_10!='' || $blank2_11!='' || $blank2_12!=''
									 || $blank2_13!='' || $blank2_14!='' || $blank2_15!='' || $blank2_16!=''
									 || $blank2_17!='' || $blank2_18!='' || $blank2_19!='' || $blank2_20!='' ){
							  $blank2Array=array($blank2_1,$blank2_2,$blank2_3,$blank2_4,$blank2_5,$blank2_6,
							  					$blank2_7,$blank2_8,$blank2_9,$blank2_10,$blank2_11,$blank2_12,
												$blank2_13,$blank2_14,$blank2_15,$blank2_16,
							  					$blank2_17,$blank2_18,$blank2_19,$blank2_20);
							  $t_blank2Array=array($t_blank2_1,$t_blank2_2,$t_blank2_3,$t_blank2_4,$t_blank2_5,$t_blank2_6,
							  					$t_blank2_7,$t_blank2_8,$t_blank2_9,$t_blank2_10,$t_blank2_11,$t_blank2_12,
												$t_blank2_13,$t_blank2_14,$t_blank2_15,$t_blank2_16,
							  					$t_blank2_17,$t_blank2_18,$t_blank2_19,$t_blank2_20);				
							 }
							 if(		$o2lpm_1!=''  || $o2lpm_2!=''  || $o2lpm_3!=''  || $o2lpm_4!=''
									 || $o2lpm_5!=''  || $o2lpm_6!=''  || $o2lpm_7!=''  || $o2lpm_8!=''
									 || $o2lpm_9!=''  || $o2lpm_10!='' || $o2lpm_11!='' || $o2lpm_12!=''
									 || $o2lpm_13!='' || $o2lpm_14!='' || $o2lpm_15!='' || $o2lpm_16!=''
									 || $o2lpm_17!='' || $o2lpm_18!='' || $o2lpm_19!='' || $o2lpm_20!='' ){
							  $o2lpmArray=array($o2lpm_1,$o2lpm_2,$o2lpm_3,$o2lpm_4,$o2lpm_5,
										  $o2lpm_6,$o2lpm_7,$o2lpm_8,$o2lpm_9,$o2lpm_10,$o2lpm_11,
										  $o2lpm_12,$o2lpm_13,$o2lpm_14,$o2lpm_15,
										  $o2lpm_16,$o2lpm_17,$o2lpm_18,$o2lpm_19,$o2lpm_20);
								$t_o2lpmArray=array($t_o2lpm_1,$t_o2lpm_2,$t_o2lpm_3,$t_o2lpm_4,$t_o2lpm_5,
										  $t_o2lpm_6,$t_o2lpm_7,$t_o2lpm_8,$t_o2lpm_9,$t_o2lpm_10,$t_o2lpm_11,
										  $t_o2lpm_12,$t_o2lpm_13,$t_o2lpm_14,$t_o2lpm_15,
										  $t_o2lpm_16,$t_o2lpm_17,$t_o2lpm_18,$t_o2lpm_19,$t_o2lpm_20);		  
							 }

							if(			$blank3_1!=''  || $blank3_2!=''  || $blank3_3!=''  || $blank3_4!=''
									 || $blank3_5!=''  || $blank3_6!=''  || $blank3_7!=''  || $blank3_8!=''
									 || $blank3_9!=''  || $blank3_10!='' || $blank3_11!='' || $blank3_12!=''
									 || $blank3_13!='' || $blank3_14!='' || $blank3_15!='' || $blank3_16!=''
									 || $blank3_17!='' || $blank3_18!='' || $blank3_19!='' || $blank3_20!='' ){
							  		$blank3Array=array($blank3_1,$blank3_2,$blank3_3,$blank3_4,$blank3_5,$blank3_6,
														$blank3_7,$blank3_8,$blank3_9,$blank3_10,$blank3_11,$blank3_12,
														$blank3_13,$blank3_14,$blank3_15,$blank3_16,
														$blank3_17,$blank3_18,$blank3_19,$blank3_20);
									$t_blank3Array=array($t_blank3_1,$t_blank3_2,$t_blank3_3,$t_blank3_4,$t_blank3_5,$t_blank3_6,
														$t_blank3_7,$t_blank3_8,$t_blank3_9,$t_blank3_10,$t_blank3_11,$t_blank3_12,
														$t_blank3_13,$t_blank3_14,$t_blank3_15,$t_blank3_16,
														$t_blank3_17,$t_blank3_18,$t_blank3_19,$t_blank3_20);
							}

							if(			$blank4_1!=''  || $blank4_2!=''  || $blank4_3!=''  || $blank4_4!=''
									 || $blank4_5!=''  || $blank4_6!=''  || $blank4_7!=''  || $blank4_8!=''
									 || $blank4_9!=''  || $blank4_10!='' || $blank4_11!='' || $blank4_12!=''
									 || $blank4_13!='' || $blank4_14!='' || $blank4_15!='' || $blank4_16!=''
									 || $blank4_17!='' || $blank4_18!='' || $blank4_19!='' || $blank4_20!='' ){
									$blank4Array=array($blank4_1,$blank4_2,$blank4_3,$blank4_4,$blank4_5,$blank4_6,
														$blank4_7,$blank4_8,$blank4_9,$blank4_10,$blank4_11,$blank4_12,
														$blank4_13,$blank4_14,$blank4_15,$blank4_16,
														$blank4_17,$blank4_18,$blank4_19,$blank4_20);
									$t_blank4Array=array($t_blank4_1,$t_blank4_2,$t_blank4_3,$t_blank4_4,$t_blank4_5,$t_blank4_6,
														$t_blank4_7,$t_blank4_8,$t_blank4_9,$t_blank4_10,$t_blank4_11,$t_blank4_12,
														$t_blank4_13,$t_blank4_14,$t_blank4_15,$t_blank4_16,
														$t_blank4_17,$t_blank4_18,$t_blank4_19,$t_blank4_20);				
							}			

					$table_print.='
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
											if(constant("ANES_OR_START_STOP_TIME")=="YES") {
												$table_print.='	
												<td style="width:100px; text-align:center;">
													<b>OR Start Time</b><br>';
													if($orStartTime){
														$table_print.=$orStartTime;	
													}else{$table_print.="____";}
												$table_print.=	
												'</td>
												<td style="width:100px; text-align:center;">
													<b>OR Stop Time</b><br>';
													if($orStopTime){
														$table_print.=$orStopTime;	
													}else{$table_print.="____";}
												$table_print.=	
												'</td>';
											}
											$table_print.='		
												<td style="width:100px; text-align:center;">
													<b>Anes&nbsp;Start&nbsp;Time</b><br>';
													if($startTime){
														$table_print.=$startTime;	
													}else{$table_print.="____";}
												$table_print.=	
												'</td>
												<td style="width:100px; text-align:center;" >
													<b>Anes&nbsp;Stop&nbsp;Time</b><br>';
													if($stopTime){
														$table_print.=$stopTime;	
													}else{$table_print.="____";}
												$table_print.=	
												'</td>';
											if(constant("ANES_OR_START_STOP_TIME")!="YES") {
												$table_print.='	
												<td style="width:100px; text-align:left;"></td>
												<td style="width:100px; text-align:left;"></td>';	
											}
											$table_print.='	
												<td style="width:75px; text-align:center;">
													<b>Start Time</b><br>';
													if($newStartTime2){
														$table_print.=$newStartTime2;	
													}else{$table_print.="____";}
												$table_print.=	
												'</td>
												<td style="width:75px; text-align:center;">
													<b>Stop Time</b><br>';
													if($newStopTime2){
														$table_print.=$newStopTime2;	
													}else{$table_print.="____";}
												$table_print.=	
												'</td>
												<td style="width:75px; text-align:center;">
													<b>Start Time</b><br>';
													if($newStartTime3){
														$table_print.=$newStartTime3;	
													}else{$table_print.="____";}
												$table_print.=	
												'</td>
												<td style="width:75px; text-align:center;" >
													<b>Stop Time</b><br>';
													if($newStopTime3){
														$table_print.=$newStopTime3;	
													}else{$table_print.="____";}
												$table_print.=	
												'</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>	
									<td style="width:700px;vertical-align:top;">
										<table style="width:700px;border:1px solid #C0C0C0;font-size:12px;" cellpadding="0" cellspacing="0">';
									for($c=1;$c<=11;$c++){
										$medLabel="";
										$medArray="";
										switch($c){
											case 1:
												$medLabel=$blank3_label;
												$medArray=$blank3Array;
												$t_medArray=$t_blank3Array;
											break;
											case 2:
												$medLabel=$blank4_label;
												$medArray=$blank4Array;
												$t_medArray=$t_blank4Array;
											break;
											case 3:
												$medLabel=$blank1_label;
												$medArray=$blank1Array;
												$t_medArray=$t_blank1Array;
											break;
											case 4:
												$medLabel=$blank2_label;
												$medArray=$blank2Array;
												$t_medArray=$t_blank2Array;
											break;
											case 5:
												$medLabel=$mgPropofol_label;
												$medArray=$propArray;
												$t_medArray=$t_propArray;
											break;
											case 6:
												$medLabel=$mgMidazolam_label;
												$medArray=$midArray;
												$t_medArray=$t_midArray;
											break;
											case 7:
												$medLabel=$mgKetamine_label;
												$medArray=$ketaArray;
												$t_medArray=$t_ketaArray;
											break;
											case 8:
												$medLabel=$mgLabetalol_label;
												$medArray=$labeArray;
												$t_medArray=$t_labeArray;
											break;
											case 9:
												$medLabel=$mcgFentanyl_label;
												$medArray=$FentanylArray;
												$t_medArray=$t_FentanylArray;
											break;
											case 10:
												$medLabel="SaO<sub>2</sub>";
												$medArray=$spoArray;
												$t_medArray=$t_spoArray;
											break;
											case 11:
												$medLabel="O<sub>2</sub>l/m";
												$medArray=$o2lpmArray;
												$t_medArray=$t_o2lpmArray;
											break;
										}
										//echo '<pre>'.$medLabel;print_r($medArray);echo '<br>';print_r($t_medArray).'</pre>';
										if($c!=10 && $c!=11){ $medLabel=htmlentities($medLabel);}
											$table_print.=
											'<tr>
												<td style="width:50px;border-right:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0; height:25px;font-weight:bold;vertical-align:middle; max-width:50px; word-break:break-all;word-wrap:break-word;" >'.$medLabel."</td>";
										for($p=0;$p<20;$p++){
											$table_print.="<td style='width:25px; border-right:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0; height:25px;font-weight:bold;text-align:center;vertical-align:middle;'>".($medArray[$p])."<br /><span style=\"font-size:8px; font-weight:normal; \">".htmlentities($t_medArray[$p])."</span></td>";
										}
									$table_print.='</tr>';
									}
									if(stripslashes($ekgBigRowValue)){
									$table_print.='
										<tr>
											<td colspan="21" class="bdrbtm" style="vertical-align:bottom;">
											<b>EKG:</b>&nbsp;'.stripslashes($ekgBigRowValue).'</td>
										</tr>';
									}
									
									
									$table_print.=
									'</table>
									</td>';
					$table_print.='
										
								</tr>
							</table>
						</td>
					</tr>';
$table_print.='</table><page></page>'; 
			if($applet_data!='' || $grid_image_path){    
								$qry="select applet_data, applet_time_interval from localanesthesiarecord where confirmation_id= $pConfId";
								$qryRes = @imw_query($qry);
								$arrayRes = @imw_fetch_array($qryRes);
								$appletData= $arrayRes['applet_data'];
								$appletTimeInterval= $arrayRes['applet_time_interval'];
								$imgNameTime="blank_timeInterval.jpg";
								$fixDateToDisplayOldApplet = '2009-06-14';
								if($confimDOS < $fixDateToDisplayOldApplet) {
									$imgName="bgGrid.jpg";
								}else {
									$imgName="bgTest.jpg";
								}
								include("imageSc/imgTimeInterval.php");
								include("imageSc/imgGd.php");
								
								//Applet data 
								$ekgRedLineThikness = 3;
								if($confimDOS < $fixDateToDisplayOldApplet) {
									drawOnImagetime($appletTimeInterval,$imgNameTime,"new_html2pdf/tess_TimeInterval.jpg");	
									drawOnImage2($appletData,$imgName,"new_html2pdf/tess.jpg",$ekgRedLineThikness);		
									$table_pdf.='<table width="100%" border="0" valign="top">
													<tr>
														<td width="100%"><img src="tess.jpg" width="600" height="370"></td>
													</tr>
												</table>';
								}else {
									
									$img_name = create_html_data_image($pConfId, $html_grid_data, $grid_image_path, $startTime); 
								}
							}
			$table_print.='
			<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:480px;vertical-align:top;">
						<table style="width:480px; " cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="4" style="width:480px;">';
									if($hide_anesthesia_grid=="Yes") {
										//DO NOT SHOW ANESTHESIA GRID
									}else {
										if(file_exists("new_html2pdf/tess_TimeInterval.jpg")){
											$table_print.="<img src='tess_TimeInterval.jpg' style='width:480px;'><br>";		
										}
										if(file_exists($img_name)){
											$table_print.="<img src='../".$img_name."' style='width:480px;'>";	
										}
									}
							$table_print.='
								</td>
							</tr>
						</table>
					</td>			
					<td style="width:260px; border-left:1px solid #C0C0C0;" valign="top">			
						<table style="width:260px;" cellpadding="0" cellspacing="0">
						<tr>	
							<td colspan="4" style="width:260px;">
								<table style="width:260px;" cellpadding="0" cellspacing="0">	
											<tr>
												<td style="width:250px;" colspan="3" class="bdrbtm">
												<b>1.&nbsp;Routine Monitors Applied:&nbsp;</b>';
												if($routineMonitorApplied!=''){
													$table_print.=$routineMonitorApplied;
												}else{$table_print.="____";}
												$table_print.='
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
														$table_print.=$ivCatheter;
													}else{$table_print.="____";}
												$table_print.='</td>
											</tr>
											<tr>
												<td style="width:80px;" class="bdrbtm bold">Hand:</td>
												<td style="width:80px;" class="bdrbtm">';
												if($hand_right=='Yes'){
													$table_print.="<b>Right</b>&nbsp;Yes";
												}else{$table_print.="__";}
												$table_print.='
												</td>
												<td style="width:80px;" class="bdrbtm">';
												if($hand_left=='Yes'){
													$table_print.="<b>Left</b>&nbsp;Yes";
												}else{$table_print.="__";}
												$table_print.='
												</td>
											</tr>
											<tr>
												<td style="width:80px;" class="bdrbtm bold">Wrist:</td>
												<td style="width:80px;" class="bdrbtm">';
												if($wrist_right=='Yes'){
													$table_print.="<b>Right</b>&nbsp;Yes";
												}else{$table_print.="__";}
												$table_print.='
												</td>
												<td style="width:80px;" class="bdrbtm">';
												if($wrist_left=='Yes'){
													$table_print.="<b>Left</b>&nbsp;Yes";
												}else{$table_print.="__";}
												$table_print.='
												</td>
											</tr>
											<tr>
												<td style="width:80px;" class="bdrbtm bold">Arm:</td>
												<td style="width:80px;" class="bdrbtm">';
												if($arm_right=='Yes'){
													$table_print.="<b>Right</b>&nbsp;Yes";
												}else{$table_print.="__";}
												$table_print.='
												</td>
												<td style="width:80px;" class="bdrbtm">';
												if($arm_left=='Yes'){
													$table_print.="<b>Left</b>&nbsp;Yes";
												}else{$table_print.="__";}
												$table_print.='
												</td>
											</tr>
											<tr>
												<td style="width:80px;" class="bdrbtm bold">Antecubital:</td>
												<td style="width:80px;" class="bdrbtm">';
												if($anti_right=='Yes'){
													$table_print.="<b>Right</b>&nbsp;Yes";
												}else{$table_print.="__";}
												$table_print.='
												</td>
												<td style="width:80px;" class="bdrbtm">';
												if($anti_left=='Yes'){
													$table_print.="<b>Left</b>&nbsp;Yes";
												}else{$table_print.="__";}
												$table_print.='
												</td>
											</tr>
											<tr>
												<td style="width:80px;" class="bold bdrbtm">Other:</td>
												<td colspan="2" style="width:160px;" class=" bdrbtm">';
												if($ivCatheterOther!=''){
													$table_print.=$ivCatheterOther;
												}else{$table_print.="_____";}
												$table_print.='	
												</td>
											</tr>
											<tr>
												<td colspan="3" class="bdrbtm bgcolor bold">3.&nbsp;Local Anesthesia</td>
											</tr>
											<tr>
												<td colspan="3" class="bdrbtm">';
												if($TopicalBlock1Block2){
													$table_print.="<b>".$TopicalBlock1Block2."</b>:&nbsp;Yes";	
												}
												$table_print.='
												</td>
											</tr>
											<tr>
												<td colspan="3">';
													if($Reblock){
														$table_print.='<b>'.$Reblock.'</b>:&nbsp;Yes';
													}
												$table_print.='
												</td>	
											</tr>
									</table>
								</td>
							</tr>	
							';
						
						
						
						
				if($TopicalBlock1Block2=='Block1' || $TopicalBlock1Block2=='Block2'){
					$table_print.='
					<tr>	
						<td colspan="4" style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm bold"><b>Aspiration:</b>&nbsp;';
						if($Block1Block2Aspiration){
							$table_print.=$Block1Block2Aspiration;	
						}else{$table_print.="___";}
						$table_print.='&nbsp;&nbsp;&nbsp;<b>Full EOM:&nbsp;</b>';
						if($Block1Block2Full=="Yes"){
							$table_print.=$Block1Block2Full;	
						}else{$table_print.="___";}
						$table_print.='
						</td>
					</tr>
					<tr>
						<td colspan="4" style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						<b>Before Injection:&nbsp;</b>';
						if($Block1Block2BeforeInjection=="Yes"){
							$table_print.=$Block1Block2BeforeInjection;
						}else{$table_print.="___";}
				$table_print.='</td>
					</tr>
					<tr>
						<td colspan="4"  style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						<b>Comments:&nbsp;</b>';
						if($Block1Block2Comment){
							$table_print.=$Block1Block2Comment;
						}else{$table_print.="___";}
						$table_print.='</td>
					</tr>';
				}
				$table_print.='
				<tr>
					<td colspan="4" style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
					<b>4% lidocaine:&nbsp;</b>';
					if($topical4PercentLidocaine=="Yes"){
						$table_print.=$topical4PercentLidocaine;
					}else{$table_print.="____";}
					$table_print.='</td>
				</tr>';
				$table_print.='
				<tr>
					<td style="width:70px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						<b>Intracameral:&nbsp;</b></td>
					<td style="width:30px;" class="bdrbtm">';
						if($Intracameral!=""){
							$table_print.=$Intracameral." ml";
						}else{$table_print.="____";}
					$table_print.='
					</td>
					<td style="width:60px;" class="bold bdrbtm">1%lidocaine MPF:&nbsp;</td>
					<td style="width:30px;" class="bdrbtm">';
						if($Intracameral1percentLidocaine=="Yes"){
							$table_print.=$Intracameral1percentLidocaine;
						}else{$table_print.="_____";}
						$table_print.='
					</td>
				</tr>
				<tr>
					<td style="width:70px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						<b>Peribulbar:&nbsp;</b></td>
					<td style="width:30px;" class="bdrbtm">';
						if($Peribulbar!=""){
							$table_print.=$Peribulbar."<br>ml";
						}else{$table_print.="____";}
					$table_print.='
					</td>
					<td style="width:60px;" class="bold bdrbtm">2% lidocaine:&nbsp;</td>
					<td style="width:30px;" class="bdrbtm">';
						if($Peribulbar2percentLidocaine=="Yes"){
							$table_print.=$Peribulbar2percentLidocaine;
						}else{$table_print.="_____";}
						$table_print.='
					</td>
				</tr>
				<tr>
					<td style="width:70px;" class="bdrbtm">
						Retrobulbar<br><span style="font-size:8px;">Done By Surgeon</span>
						</td>
					<td style="width:30px;" class="bdrbtm">';
						if($Retrobulbar!=""){
							$table_print.=$Retrobulbar." mls";
						}else{$table_print.="___";}
					$table_print.='
					</td>
					<td style="width:60px;" class="bold bdrbtm">3% lidocaine</td>
					<td style="width:30px;" class="bdrbtm">';
						if($Retrobulbar4percentLidocaine=="Yes"){
							$table_print.=$Retrobulbar4percentLidocaine;
						}else{$table_print.="___";}
						$table_print.='
					</td>
				</tr>
				<tr>
					<td colspan="4" style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
					<b>4% lidocaine:&nbsp;</b>';
					if($Hyalauronidase4percentLidocaine=="Yes"){
						$table_print.=$Hyalauronidase4percentLidocaine;
					}else{$table_print.="___";}
					$table_print.=
					'</td>
				</tr>
				<tr>
					<td style="width:70px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						Van Lindt:&nbsp;</td>
					<td style="width:30px;" class="bdrbtm">';
						if($VanLindr!="" || $VanLindrHalfPercentLidocaine=="Yes"){
							$table_print.=$VanLind."<br>mls";
						}else{$table_print.="____";}
					$table_print.='
					</td>
					<td style="width:60px;" class="bold bdrbtm">0.5% Bupivacaine:&nbsp;</td>
					<td style="width:30px;" class="bdrbtm">';
						if($VanLindrHalfPercentLidocaine=="Yes"){
							$table_print.=$VanLindrHalfPercentLidocaine;
						}else{$table_print.="____";}
						$table_print.='
					</td>
				</tr>';
			if($version_num > 1)
			{
				$table_print.='
					<tr>
						<td colspan="4" style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						<b>0.75% Bupivacaine:&nbsp;</b>';
						if($bupivacaine75=="Yes"){
							$table_print.=$bupivacaine75;
						}else{$table_print.="___";}
						$table_print.=
						'</td>
					</tr>
					
					<tr>
						<td colspan="4" style="width:240px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						<b>0.75% Marcaine:&nbsp;</b>';
						if($marcaine75=="Yes"){
							$table_print.=$marcaine75;
						}else{$table_print.="___";}
						$table_print.=
						'</td>
					</tr>';
			}
				
			$table_print.='
				<tr>
					<td colspan="2" style="width:100px;border-bottom:1px solid #C0C0C0;" class="bdrbtm">
						';
						if($lidTxt!="" || $lidEpi5ug=="Yes"){
							$table_print.=$lidTxt." lid ".$lid." mls";
						}else{$table_print.="____";}
					$table_print.='
					</td>
					<td style="width:60px;" class="bold bdrbtm">Epi 5 ug/ml:&nbsp;</td>
					<td style="width:30px;" class="bdrbtm">';
						if($lidEpi5ug=="Yes"){
							$table_print.=$lidEpi5ug;
						}else{$table_print.="____";}
						$table_print.='
					</td>
				</tr>
				<tr>
					<td colspan="2" style="width:100px;" class="bdrbtm">
						Other:&nbsp;';
						if($otherRegionalAnesthesiaTxt1!='' ||$otherRegionalAnesthesiaDrop!=""){
							$table_print.=$otherRegionalAnesthesiaTxt1." ".$otherRegionalAnesthesiaDrop."mls";
						}
					$table_print.='
					</td>
					<td style="width:60px;font-size:12px;" class="bold bdrbtm">Wydase 15 u/ml:&nbsp;</td>
					<td style="width:30px;font-size:12px;" class="bdrbtm">';
						if($otherRegionalAnesthesiaWydase15u=="Yes"){
							$table_print.=$otherRegionalAnesthesiaWydase15u;
						}else{$table_print.="____";}
						$table_print.='
					</td>
				</tr>
				<tr>
					<td colspan="4" style="width:240px;" class="bdrbtm">Other:&nbsp;';
					if($otherRegionalAnesthesiaTxt2!=''){
						$table_print.=$otherRegionalAnesthesiaTxt2;
					}else{$table_print.="___";}
					$table_print.=	
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm bgcolor bold">';
					$table_print.='4.&nbsp;Ocular Pressure';
					if($ocular_pressure_na=='Yes'){
						$table_print.="&nbsp;&nbsp;&nbsp;&nbsp;N/A:&nbsp;<b>Yes</b>";
					}
					$table_print.=	
					'</td>
				</tr>	
				<tr>
					<td colspan="4" class="bdrbtm"><b>None:&nbsp;</b>';
					if($none=='Yes'){
						$table_print.=$none;
					}else{$table_print.="____";}
					$table_print.=
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm"><b>Digital:&nbsp;</b>';
					if($digital=='Yes'){
						$table_print.=$digital;
					}else{$table_print.="____";}
					$table_print.=
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm"><b>Honan Balloon:&nbsp;</b>';
					if($honanballon!='' || $honanBallonAnother!=''){
						$table_print.=$honanballon." mm ".$honanBallonAnother." Min";
					}else{$table_print.="____";}
					$table_print.=
					'</td>
				</tr>
				<tr>
					<td colspan="4" style="width:240px;" class="bdrbtm"><b>Comment:</b>&nbsp;';
					if($ansComment!=''){
						$table_print.=$ansComment;
					}else{$table_print.="______";}
					$table_print.=
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm">';
						if($signAnesthesia2Status=="Yes"){
							$table_print.="<br><b>Anesthesia Provider:&nbsp;</b>".$signAnesthesia2LastName.', '.$signAnesthesia2FirstName;
							$table_print.="<br><b>Electronically Signed:&nbsp;</b>Yes";
							$table_print.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signAnesthesia2DateTime);
							
						}else {
							$table_print.="<br><b>Anesthesia Provider:&nbsp;</b>________";
							$table_print.="<br><b>Electronically Signed:&nbsp;</b>________";
							$table_print.="<br><b>Signature Date:&nbsp;</b>________";
						}
						$table_print.=
					'</td>
				</tr>
				<tr>
					<td colspan="4" class="bdrbtm"><b>Anesthesia Provider:</b>&nbsp;';
					if($relivedIntraNurseId>0){
						$qrnurse="select lname,fname from users where usersId='$relivedIntraNurseId'";
						$qrRes=imw_query($qrnurse) ;	
						$nursenameres=imw_fetch_array($qrRes);
						$nurseName=	$nursenameres['lname'].',&nbsp;'.$nursenameres['fname'];						
						$table_print.=$nurseName;					
					}else{$table_print.="_____";}
					$table_print.=
					'</td>
				</tr>';
			$table_print.=			
			'</table>
		</td>
	</tr>';
							// Vital Sign Grid Printing Section - Start
			
			
							if($vitalSignGridStatus)
							{
									$table_print.='<tr>';
									$table_print.='<td style="width:700px;" class="bdrbtm" colspan="2" valign="top"><table style="width:700px; " cellpadding="0" cellspacing="0">';
									
									$table_print.='<tr>';
									$table_print.='<td class="bgcolor bold bdrbtm" colspan="8" style="width:700px; vertical-align:middle; ">&nbsp;&nbsp;Vital Signs</td>';
									$table_print.='</tr>';
									
									
									$table_print.='<tr>';	
									$table_print.='<td rowspan="2" class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Time</b></td>';
									$table_print.='<td colspan="2" class="bdrbtm" style="width:200px; border-right:1px solid #C0C0C0; vertical-align:middle;">&nbsp;<b>B/P</b></td>';
									$table_print.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Pulse</b></td>';
									$table_print.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>RR</b></td>';
									$table_print.='<td rowspan="2" class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Temp<sup>O</sup> C</b></td>';
									$table_print.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>EtCO<sub>2</sub></b></td>';
									$table_print.='<td rowspan="2" class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;<b>OSat<sub>2</sub></b></td>';		
									$table_print.='</tr>';
									$table_print.='<tr>';	
									
									$table_print.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Systolic</b></td>';
									$table_print.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Diastolic</b></td>';
									
									$table_print.='</tr>';
										
									
									
									$condArr		=	array();
									$condArr['confirmation_id']	=	$pConfId ;
									$condArr['chartName']				=	'mac_regional_anesthesia_form' ;
									
									$gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time','Asc');
						
									$gCounter	=	0;
									if(is_array($gridData) && count($gridData) > 0  )
									{
										foreach($gridData as $gridRow)
										{		
											$gCounter++;
											//$fieldValue1= date('h:i A', strtotime($gridRow->start_time));
											$fieldValue1= $objManageData->getTmFormat($gridRow->start_time);
											$fieldValue2= stripslashes($gridRow->systolic);
											$fieldValue3= stripslashes($gridRow->diastolic);
											$fieldValue4= stripslashes($gridRow->pulse);
											$fieldValue5= stripslashes($gridRow->rr);
											$fieldValue6= stripslashes($gridRow->temp);
											$fieldValue7= stripslashes($gridRow->etco2);
											$fieldValue8= stripslashes($gridRow->osat2);
											
											$table_print.='<tr>';	
											$table_print.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue1.'</td>';
											$table_print.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue2.'</td>';
											$table_print.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue3.'</td>';
											$table_print.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue4.'</td>';
											$table_print.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue5.'</td>';
											$table_print.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue6.'</td>';
											$table_print.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue7.'</td>';
											$table_print.='<td class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;'.$fieldValue8.'</td>';		
											$table_print.='</tr>';
												
										}
									}
									
									for($loop = $gCounter; $loop < 3; $loop++)
									{
											$table_print.='<tr>';	
											$table_print.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_print.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_print.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_print.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_print.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_print.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_print.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
											$table_print.='<td class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;</td>';		
											$table_print.='</tr>';
									}
									$table_print.='</table></td>';
									$table_print.='</tr>';
												
							}
							// Vital Sign Grid Printing Section - End
	
	
	$table_print.=	
	'<tr>
		<td colspan="2" style="width:700px;" class="bgcolor bold bdrbtm">Post-Operative</td>
	</tr>
	<tr>
		<td colspan="2" style="width:700px;">
			<table style="width:700px;" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:350px;border-right:1px solid #C0C0C0;">
						<table style="width:350px;" cellpadding="0" cellspacing="0">
							<tr>	
								<td style="width:350px;" class="bdrbtm">
									<b>No known anesthetic complication:&nbsp;</b>';
									if($anyKnowAnestheticComplication){
										$table_print.=$anyKnowAnestheticComplication;
									}else{$table_print.="___";}
								$table_print.='
								</td>
							</tr>
							<tr>	
								<td style="width:350px;" class="bdrbtm">
									<b>Stable cardiovascular and pulmonary function:&nbsp;</b>';
									if($stableCardiPlumFunction2){
										$table_print.=$stableCardiPlumFunction2;
									}else{$table_print.="___";}
								$table_print.='
								</td>
							</tr>
							<tr>	
								<td style="width:350px;">
									<b>Satisfactory condition for discharge:&nbsp;</b>';
									if($satisfactoryCondition4Discharge){
										$table_print.=$satisfactoryCondition4Discharge;
									}else{$table_print.="___";}
								$table_print.='
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
									$table_print.=htmlentities(stripslashes($evaluation));
								}else{
									$table_print.="____";	
								}
								$table_print.=
								'</td>
							</tr>
							<tr>	
								<td style="width:80px;" class="bold">Remarks:</td>
								<td style="width:300px;">';
								if(trim($remarks)){
									$table_print.=htmlentities(stripslashes($remarks));
								}else{
									$table_print.="____";	
								}
								$table_print.=
								'</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>';
	
	// Start Printing Additional Question
	$table_print .= $objLocalAnesData->mac_ques_print_html($pConfId);
	// End Printing Additional Question

	$table_print.=
	'<tr>
		<td colspan="2" style="width:700px;">
			<table style="width:700px;" cellpadding="0" cellspacing="0">				
				<tr>
					<td style="width:350px;" class="bdrbtm pl5">';
					if($signAnesthesia3Status=="Yes"){
						$table_print.="<b>Anesthesia Provider:&nbsp;</b>".$signAnesthesia3LastName.', '.$signAnesthesia3FirstName;
						$table_print.="<br><b>Electronically Signed:&nbsp;</b>Yes";
						$table_print.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signAnesthesia3DateTime);
					}else {
						$table_print.="<b>Anesthesia Provider:&nbsp;</b>________";
						$table_print.="<br><b>Electronically Signed:&nbsp;</b>________";
						$table_print.="<br><b>Signature Date:&nbsp;</b>________";
					}
					
					$table_print.='
					</td>
					<td style="width:350px;" class="bdrbtm pl5">';
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
					$table_print.='<b>'.$userTypeLabel.':&nbsp;</b>'.$nurseName2;
					
					$table_print.=
					'</td>
				</tr>
			</table>
		</td>
	</tr>';
	}
$table_print.='</table>';
if($headerBar=='yes') {
	$table_print.='</page>';
	$table_print=$objManageData->replacePageTag($table_print,$headerBarTable);
}	
$fp = fopen('new_html2pdf/pdffile.html','w+');
$intBytes = fputs($fp,$table_print);
fclose($fp);
//die($table_print);
?>	

 <form name="printlocal_anes" action="new_html2pdf/createPdf.php?op=p" method="post">
 </form> 
<script language="javascript">
	function submitfn(){
		document.printlocal_anes.submit();
	}
</script>

<?php 
if($localanesFormStatus=='completed' || $localanesFormStatus=='not completed') {
	echo '<table id="loader_tbl" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">Please wait while data is retrieving from the server.</td>
			</tr>
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center"><img src="images/pdf_load_img.gif"></td> 
			</tr>
		</table>';
	
	//if(trim($grid_image_path)) {
?>
		<!--<iframe src="local_anes_record.php?patient_id=<?php echo $patient_id;?>&pConfId=<?php echo $_REQUEST['pConfId'];?>&printAnesthesiaGridFrame=yes" style="height:0px; width:0px;"></iframe>-->
<?php		
	//}else {
?>
		<script type="text/javascript">
            submitfn();
        </script>
<?php
	//}
}else {
echo "<center>Please verify/save this form before print</center>";
}

//echo $table_print;
?>