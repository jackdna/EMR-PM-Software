<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php session_start();?>
<table bgcolor="#FFFFFF"  style="font:Verdana, Geneva, sans-serif; font-size:22px;" width="100%" height="80%">
	<tr>
		<td width="100%" align="center" valign="middle"><b>please wait..printing is in process</b><br /><img src="images/ajax-loader.gif"></td> 
	</tr>
</table>
<?php	
	include_once("common/conDb.php");
	$blEnableHTMLGrid = false;
	$enableFooterPaging = (constant('PRINT_ALL_PAGING') == 'NO') ? false : true;
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
	
	include("common_functions.php");
	include_once("common/commonFunctions.php");
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
	include_once("new_header_print.php");
	$pConfId = $_REQUEST['pConfId'];
	if(!$pConfId) {
		$pConfId = $_SESSION['pConfId']; 
	}
	//START GET VOCABULARY OF ASC
	$ascInfoArr = $objManageData->getASCInfo($_SESSION["facility"]);
	$settings = $objManageData->loadSettings('asa_4,anes_mallampetti_score');
	//END GET VOCABULARY OF ASC
	
	$Consent_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$pConfId."'";
	$Consent_patientConfirm_tblRes = imw_query($Consent_patientConfirm_tblQry) or die(imw_error());
	$Consent_patientConfirm_tblRow = imw_fetch_array($Consent_patientConfirm_tblRes);
	$finalizeStatus = $Consent_patientConfirm_tblRow["finalize_status"];
	$allergiesNKDA_status = $Consent_patientConfirm_tblRow["allergiesNKDA_status"];
	$noMedicationStatus = $Consent_patientConfirm_tblRow["no_medication_status"];
	$noMedicationComments = $Consent_patientConfirm_tblRow["no_medication_comments"];
	
	$patientId = $patient_id = $Consent_patientConfirm_tblRow["patientId"];
	if(!$patientId) {
		$patientId = $patient_id=$_SESSION['patient_id'];
	}

	//START GET ARRIVAL TIME
	$stubWaitingDetailArr = $objManageData->getStubWaitingDetail($_REQUEST['pConfId'],$Consent_patientConfirm_tblRow["dos"])	;
	$arrivalTime = ($stubWaitingDetailArr[0]) ? $stubWaitingDetailArr[0] : '';	
	//END GET ARRIVAL TIME

	
	//GET PATIENT DETAIL
	$Consent_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
	$Consent_patientName_tblRes = imw_query($Consent_patientName_tblQry) or die(imw_error());
	$Consent_patientName_tblRow = imw_fetch_array($Consent_patientName_tblRes);
	$Consent_patientName = $Consent_patientName_tblRow["patient_lname"].", ".$Consent_patientName_tblRow["patient_fname"]." ".$Consent_patientName_tblRow["patient_mname"];
	
	
	$Consent_patientNameDobTemp = $Consent_patientName_tblRow["date_of_birth"];
	$Consent_patientNameDob_split = explode("-",$Consent_patientNameDobTemp);
	$Consent_patientNameDob = $Consent_patientNameDob_split[1]."-".$Consent_patientNameDob_split[2]."-".$Consent_patientNameDob_split[0];
	

	$Consent_patientConfirmDosTemp = $Consent_patientConfirm_tblRow["dos"];
	$Consent_patientConfirmDos_split = explode("-",$Consent_patientConfirmDosTemp);
	$Consent_patientConfirmDos = $Consent_patientConfirmDos_split[1]."-".$Consent_patientConfirmDos_split[2]."-".$Consent_patientConfirmDos_split[0];

	$Consent_patientConfirmSurgeon = $Consent_patientConfirm_tblRow["surgeon_name"];
	$Consent_patientConfirmSiteTemp = $Consent_patientConfirm_tblRow["site"];
	// APPLYING NUMBERS TO PATIENT SITE
		if($Consent_patientConfirmSiteTemp == 1) {
			$Consent_patientConfirmSite = "Left Eye";  //OD
		}else if($Consent_patientConfirmSiteTemp == 2) {
			$Consent_patientConfirmSite = "Right Eye";  //OS
		}else if($Consent_patientConfirmSiteTemp == 3) {
			$Consent_patientConfirmSite = "Both Eye";  //OU
		}
	// END APPLYING NUMBERS TO PATIENT SITE
	$Consent_patientConfirmPrimProc = $Consent_patientConfirm_tblRow["patient_primary_procedure"];
	$Consent_patientConfirmSecProc = $Consent_patientConfirm_tblRow["patient_secondary_procedure"];
	//END GET PATIENT DETAIL
	
	//VIEW RECORD FROM DATABASE
	$ViewConsentSurgeryQry = "select * from `surgery_consent_form` where  confirmation_id = '".$pConfId."'";
	$ViewConsentSurgeryRes = imw_query($ViewConsentSurgeryQry) or die(imw_error()); 
	$ViewConsentSurgeryNumRow = @imw_num_rows($ViewConsentSurgeryRes);
	$ViewConsentSurgeryRow = imw_fetch_array($ViewConsentSurgeryRes); 
	
	$consentSurgery_patient_sign = $ViewConsentSurgeryRow["surgery_consent_sign"];
	$surgery_consent_data = stripslashes($ViewConsentSurgeryRow["surgery_consent_data"]);
	$form_status = $ViewConsentSurgeryRow["form_status"];
	$saveLink = $saveLink."&form_status=".$form_status;
	
	//image for signature
	$preid=$pConfId;
	$pertable = 'surgery_consent_form';
	$preidName = 'confirmation_id';
	$predocSign = 'surgery_consent_sign';
	$qry = "select surgery_consent_sign from surgery_consent_form where confirmation_id = $pConfId";
	$pixleRes = imw_query($qry);
	list($surgery_consent_sign) = imw_fetch_array($pixleRes);
	require_once("imgGd.php");
	drawOnImage($surgery_consent_sign,$imgName,'12.jpg');
	/*******End of signature*****/
	
//============Get All User in Array=========================//
	$qryUser="select usersId,fname,mname,lname,user_type,signature from users";
	$qryRes=imw_query($qryUser) or die(imw_error());
	$userNameArr=array();
	if(imw_num_rows($qryRes)) {
		while($qryrow=imw_fetch_array($qryRes)) {
			$usersId = $qryrow["usersId"];
			$userNameArr[$usersId]=$qryrow['lname'].','.$qryrow['fname'].' '.$qryrow['mname'];
			$userTypeArr[$usersId]=$qryrow['user_type'];
			$userSignArr[$usersId]=$qryrow['signature'];
		}
	}
//============================================================//

//GET RECORDS FROM operatingroomrecords TO USE IN MULTIPLE CHARTS
$oproomQry					= "SELECT *, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat, date_format(signNurse1DateTime,'%m-%d-%Y %h:%i %p') as signNurse1DateTimeFormat FROM operatingroomrecords WHERE confirmation_id='".$_REQUEST["pConfId"]."' LIMIT 0,1";
$oproomRec					= imw_query($oproomQry) or die($oproomQry.imw_error());
$oproomNum 					= imw_num_rows($oproomRec);
$surgeryTimeIn = $surgeryStartTime = $surgeryEndTime = $surgeryTimeOut = "";
if($oproomNum>0) {
	$ViewOpRoomRecordRow	= imw_fetch_array($oproomRec);
	$surgeryTimeIn 			= $objManageData->getTmFormat($ViewOpRoomRecordRow["surgeryTimeIn"]);
	$surgeryStartTime 		= $objManageData->getTmFormat($ViewOpRoomRecordRow["surgeryStartTime"]);
	$surgeryEndTime 		= $objManageData->getTmFormat($ViewOpRoomRecordRow["surgeryEndTime"]);
	$surgeryTimeOut 		= $objManageData->getTmFormat($ViewOpRoomRecordRow["surgeryTimeOut"]);
}

$table='';
//*********************[Start Check_list Form]*********************************//
			if($pConfId){
				$getCheckListDetail = $objManageData->getExtractRecord('surgical_check_list', 'confirmation_id', $pConfId);	
			}
			if(is_array($getCheckListDetail)){
				extract($getCheckListDetail);
			}
			if($form_status){
				
				$table.="<page>".$head_table;
				
				if($checklist_old_new == 'old')
				{
					include 'check_list_printpop_previous.php';	
				}
				else
				{
					include 'check_list_printpop_new.php';	
				}
				$table.="</page>";
			}
//================End Check List=======================================//



//================Start Multiple Consent Form==========================//


	$ViewConsentSurgeryQry = "select * from `consent_multiple_form` where  confirmation_id = '".$pConfId."' AND consent_template_id!='0' ORDER BY consent_template_id";
	$ViewConsentSurgeryRes = imw_query($ViewConsentSurgeryQry) or die(imw_error()); 
	$ViewConsentSurgeryNumRow = @imw_num_rows($ViewConsentSurgeryRes);
	if($ViewConsentSurgeryNumRow>0) {
		$cntrInc = 0;
		while($ViewConsentSurgeryRow = imw_fetch_array($ViewConsentSurgeryRes)) {
			$cntrInc++;
			
			$surgery_consent_data = stripslashes($ViewConsentSurgeryRow["surgery_consent_data"]);
			//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE 
			$consentMultipleId = $ViewConsentSurgeryRow["consent_template_id"];
			$consent_purge_status= $ViewConsentSurgeryRow["consent_purge_status"];
			$lable = $ViewConsentSurgeryRow["surgery_consent_alias"];
			
			
			//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
				$surgery_consent_data= str_ireplace("&#39;","'",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{PATIENT ID}","<b>".$Consent_patientName_tblRow["patient_id"]."</b>",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{PATIENT FIRST NAME}","<b>".$Consent_patientName_tblRow["patient_fname"]."</b>",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{MIDDLE INITIAL}","<b>".$Consent_patientName_tblRow["patient_mname"]."</b>",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{LAST NAME}","<b>".$Consent_patientName_tblRow["patient_lname"]."</b>",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{DOB}","<b>".$Consent_patientNameDob."</b>",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{SURGEON NAME}","<b>".$Consent_patientConfirmSurgeon."</b>",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{ARRIVAL TIME}", '<b>'.$arrivalTime.'</b>', $surgery_consent_data);
				$surgery_consent_data= str_ireplace("{SITE}","<b>".$Consent_patientConfirmSite."</b>",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{PROCEDURE}","<b>".$Consent_patientConfirmPrimProc."</b>",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$surgery_consent_data);
				$surgery_consent_data= str_ireplace("{DATE}","<b>".date('m-d-Y')."</b>",$surgery_consent_data);
				$surgery_consent_data = str_ireplace('{SIGNATURE}',"",$surgery_consent_data);
				
				$surgery_consent_data = str_ireplace('{TEXTBOX_XSMALL}',"<input type='text' name='xsmall' size='1' maxlength='1'>",$surgery_consent_data);
				$surgery_consent_data = str_ireplace('{TEXTBOX_SMALL}',"<input type='text' name='small' size='30' maxlength='30'>",$surgery_consent_data);
				$surgery_consent_data = str_ireplace('{TEXTBOX_MEDIUM}',"<input type='text' name='medium' size='60' maxlength='60'>",$surgery_consent_data);
				$surgery_consent_data = str_ireplace('{TEXTBOX_LARGE}',"<textarea name='large' cols='100' rows='2'></textarea>",$surgery_consent_data);
				$surgery_consent_data = str_ireplace('SigPlus_images/',"../SigPlus_images/",$surgery_consent_data);
				$surgery_consent_data = str_ireplace('html2pdfnew/',"../html2pdfnew/",$surgery_consent_data);
				$surgery_consent_data = str_ireplace('/'.$surgeryCenterDirectoryName.'/new_html2pdf/',"",$surgery_consent_data);
				//$surgery_consent_data = str_ireplace('{SIGNATURE}',"",$surgery_consent_data);
				
				//SET TEXT BOX VALUES
				$surgery_consent_data = str_ireplace('<input type="text" name="xsmall" value="','',$surgery_consent_data);
				$surgery_consent_data = str_ireplace('" size="1" >','',$surgery_consent_data);
				$surgery_consent_data = str_ireplace('<input type="text" name="small" value="','',$surgery_consent_data);
				$surgery_consent_data = str_ireplace('" size="30" >','',$surgery_consent_data);
				$surgery_consent_data = str_ireplace('<input type="text" name="medium" value="','',$surgery_consent_data);
				$surgery_consent_data = str_ireplace('" size="60" >','',$surgery_consent_data);
				//REPLACE MULTIPLE VERY SMALL TEXTBOX
				$verySmallTextBoxExplode = explode('name="xsmall',$surgery_consent_data);
				for($i=1;$i<count($verySmallTextBoxExplode);$i++) {
					
					$verySmallTextbox = 'xsmall'.$i;
					$surgery_consent_data = str_replace('<input type="text"  name="'.$verySmallTextbox.'" value="','',$surgery_consent_data);
					$surgery_consent_data = str_replace('" size="1"  maxlength="1">','',$surgery_consent_data);
				}

				//REPLACE MULTIPLE VERY SMALL TEXTBOX
				
				//REPLACE MULTIPLE SMALL TEXTBOX
				$smallTextBoxExplode = explode('name="small',$surgery_consent_data);
				for($j=1;$j<count($smallTextBoxExplode);$j++) {
					
					$smallTextbox = 'small'.$j;
					$surgery_consent_data = str_replace('<input type="text"  name="'.$smallTextbox.'" value="','',$surgery_consent_data);
					$surgery_consent_data = str_replace('" size="30"  maxlength="30">','',$surgery_consent_data);
				}
				//REPLACE MULTIPLE SMALL TEXTBOX
				
				//REPLACE MULTIPLE MEDIUM TEXTBOX
				$mediumTextBoxExplode = explode('name="medium',$surgery_consent_data);
				for($k=1;$k<count($mediumTextBoxExplode);$k++) {
					
					$mediumTextbox = 'medium'.$k;
					$surgery_consent_data = str_replace('<input type="text"  name="'.$mediumTextbox.'" value="','',$surgery_consent_data);
					$surgery_consent_data = str_replace('" size="60"  maxlength="60">','',$surgery_consent_data);
				}
						
				if(trim($surgery_consent_data)!='') {
					$surgery_consent_data = nl2br($surgery_consent_data);
					$table.="<page>".$head_table;
					$table.='<table style="width:740px;" cellpadding="0" cellspacing="0">';
					if($consentPurgeStatus=="true"){
						$table.='<tr><td style="color:#FF0000;width:700px; height:20px;" class="cbold"><u>CONSENT FORM PURGED</u></td></tr>';
					}
				   $table.='<tr>
								<td style="width:740px;" class="fheader">'.$lable.'</td>
						   </tr>
						   <tr>
								<td style="width:740px;" class="cbold">Patient Form</td>
						   </tr>
						   	
					   </table>';
					//START CODE FOR SURGEON AND NURSE SIGN
					
					$signSurgeon1Activate = $ViewConsentSurgeryRow["signSurgeon1Activate"];
					$signSurgeon1ConsentId = $ViewConsentSurgeryRow["signSurgeon1Id"];
					$Surgeon1ConsentName = $ViewConsentSurgeryRow["signSurgeon1LastName"].", ".$ViewConsentSurgeryRow["signSurgeon1FirstName"]." ".$ViewConsentSurgeryRow["signSurgeon1MiddleName"];
					$Surgeon1ConsentSignOnFileStatus = $ViewConsentSurgeryRow["signSurgeon1Status"];
					$signSurgeon1DateTime = $ViewConsentSurgeryRow["signSurgeon1DateTime"];
					
					$signNurseActivate = $ViewConsentSurgeryRow["signNurseActivate"];
					$signNurseConsentId = $ViewConsentSurgeryRow["signNurseId"];
					$NurseConsentNameShow = $ViewConsentSurgeryRow["signNurseLastName"].", ".$ViewConsentSurgeryRow["signNurseFirstName"]." ".$ViewConsentSurgeryRow["signNurseMiddleName"];
					$NurseConsentSignOnFileStatus = $ViewConsentSurgeryRow["signNurseStatus"];
					$signNurseDateTime = $ViewConsentSurgeryRow["signNurseDateTime"];
					
					if($signSurgeon1Activate=='yes' || $signNurseActivate=='yes'){
						$surgery_consent_data.='
							<table style="width:700px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:400px;"><table cellpadding="0" cellspacing="0" border="0">';
								if($signSurgeon1Activate=='yes' && $signSurgeon1ConsentId) { 
									$surgery_consent_data.='
													<tr><td><b>Surgeon:</b> Dr '.$Surgeon1ConsentName.'</td></tr>
													<tr><td><b>Electronically Signed :&nbsp;</b>'.$Surgeon1ConsentSignOnFileStatus.'</td></tr>
													<tr><td><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signSurgeon1DateTime).'</td></tr>';
								}
							$surgery_consent_data.='
									</table>
								</td>
								<td style="width:300px;"><table cellpadding="0" cellspacing="0" border="0">';	
									if($signNurseActivate=='yes' && $signNurseConsentId) { 
										$surgery_consent_data.='
													<tr><td><b>Nurse:</b> '.$NurseConsentNameShow.'</td></tr>
													<tr><td><b>Electronically Signed :&nbsp;</b>'.$NurseConsentSignOnFileStatus.'</td></tr>
													<tr><td><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signNurseDateTime).'</td></tr>';
								}
						$surgery_consent_data.='
									</table>
								</td>
							</tr>
						</table>';
					}
						 
					//END CODE FOR SURGEON AND NURSE SIGN
					$signAnesthesia1Activate 	= $ViewConsentSurgeryRow["signAnesthesia1Activate"];
					$signAnesthesia1Id 			= $ViewConsentSurgeryRow["signAnesthesia1Id"];
					$AnesthesiaConsentNameShow 	= $ViewConsentSurgeryRow["signAnesthesia1LastName"].", ".$ViewConsentSurgeryRow["signAnesthesia1FirstName"]." ".$ViewConsentSurgeryRow["signAnesthesia1MiddleName"];
					$signAnesthesia1Status 		= $ViewConsentSurgeryRow["signAnesthesia1Status"];
					$signAnesthesia1DateTime 	= $ViewConsentSurgeryRow["signAnesthesia1DateTime"];
					
					$signWitness1Activate 		= $ViewConsentSurgeryRow["signWitness1Activate"];
					$signWitness1Id 			= $ViewConsentSurgeryRow["signWitness1Id"];
					$WitnessConsentNameShow 	= $ViewConsentSurgeryRow["signWitness1LastName"].", ".$ViewConsentSurgeryRow["signWitness1FirstName"]." ".$ViewConsentSurgeryRow["signWitness1MiddleName"];
					$signWitness1Status 		= $ViewConsentSurgeryRow["signWitness1Status"];
					$signWitness1DateTime 		= $ViewConsentSurgeryRow["signWitness1DateTime"];
				
				//	CODE FOR ANESTHESIA AND WITNESS SIGN
						if($signAnesthesia1Activate=='yes' || $signWitness1Activate=='yes') {
						$surgery_consent_data.='
							<table style="width:700px;margin-top:5px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:400px;" ><table cellpadding="0" cellspacing="0" border="0">
							';
								if($signAnesthesia1Activate=='yes' && $signAnesthesia1Id) { 
									$Anesthesia1PreFix = 'Dr';
									$Anesthesia1SubType = getUserSubTypeFun($signAnesthesia1Id); //FROM common/commonFunctions.php
									if($Anesthesia1SubType=='CRNA') {$Anesthesia1PreFix = ''; }
									
									$surgery_consent_data.='
													<tr><td><b>Anesthesia Provider:</b> '.$Anesthesia1PreFix.' '.$AnesthesiaConsentNameShow.'</td></tr>
													<tr><td><b>Electronically Signed :&nbsp;</b>'.$signAnesthesia1Status.'</td></tr>
													<tr><td><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signAnesthesia1DateTime).'</td></tr>';
								}
								$surgery_consent_data.='
										</table>
									</td>
									<td style="width:300px;"><table cellpadding="0" cellspacing="0" border="0">';	
								if($signWitness1Activate=='yes' && $signWitness1Id) { 
									$surgery_consent_data.='
													<tr><td><b>Witness:</b> '.$WitnessConsentNameShow.'</td></tr>
													<tr><td><b>Electronically Signed :&nbsp;</b>'.$signWitness1Status.'</td></tr>
													<tr><td><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signWitness1DateTime).'</td></tr>';
								}
						$surgery_consent_data.='
										</table>
									</td>
								</tr>
							</table>';
					}
	//END CODE FOR ANESTHESIA AND WITNESS SIGN
					
					$surgery_consent_data= str_ireplace("{Surgeon's Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Surgeon's&nbsp;Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Surgeon&#39;s Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Surgeon&#39;s&nbsp;Signature}"," ",$surgery_consent_data);
					
					$surgery_consent_data= str_ireplace("{Nurse's Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Nurse's&nbsp;Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Nurse&#39;s Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Nurse&#39;s&nbsp;Signature}"," ",$surgery_consent_data);
						
					$surgery_consent_data= str_ireplace("{Anesthesiologist's Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Anesthesiologist's&nbsp;Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Anesthesiologist&#39;s Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Anesthesiologist&#39;s&nbsp;Signature}"," ",$surgery_consent_data);
					
					
					$surgery_consent_data= str_ireplace("{Witness Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Witness&nbsp;Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Witness&#39;s Signature}"," ",$surgery_consent_data);
					$surgery_consent_data= str_ireplace("{Witness&#39;s&nbsp;Signature}"," ",$surgery_consent_data);
	
					$table.= strip_tags($surgery_consent_data,'<img> <u> <b> <strong> <p> <div> <table> <tbody> <tr> <td> <page> ');					
					$table = str_ireplace('&nbsp;<br />','',$table);
					$table = str_ireplace('<p>&nbsp;</p>','',$table);
					
					$table.='<table cellpadding="0" cellspacing="0" style="width:700px;">';
					if($consentPurgeStatus=="true"){
						$table.='<tr><td style="color:#FF0000;width:700px; height:20px;" class="cbold"><u>CONSENT FORM PURGED</u></td></tr>';
					}else{
						$table.='<tr><td class="bdrbtm">&nbsp;</td></tr>';
					}
			$table.='</table>
				</page>';		   
				}	
			}
		}	
	
//======================End Multiple Consent Form==========================//	



//=====================Pre-Op Health Questionnior========================//
//adminHealthquestionare

	$selectAdminQuestionsQry="select * from healthquestioner";
	$selectAdminQuestions=imw_query($selectAdminQuestionsQry);
	$selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
	$i=0;
	while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions))
	{
	foreach($ResultselectAdminQuestions as $key=>$value){
			$question[$i][$key]=$value;
		}
		$i++;	
	}
	
	//echo $question[2]['question'];
//End adminHealthquestionare


 	$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'preophealthquestionnaire' AND patient_conf_id = '$pConfId' ";
	$rsNotes =imw_query($query_rsNotes);
	$totalRows_rsNotes =imw_num_rows($rsNotes);


	//$table = 'allergies';
	//include("common/pre_defined_popup.php");
	if($preOpHealthQuesId){
		$getPreOpQuesDetails = $objManageData->getExtractRecord("preophealthquestionnaire", "preOpHealthQuesId", $preOpHealthQuesId, " *, date_format(signWitness1DateTime,'%m-%d-%Y %h:%i %p') as signWitness1DateTimeFormat ");
	}else if($pConfId){
		$getPreOpQuesDetails = $objManageData->getExtractRecord("preophealthquestionnaire", "confirmation_id", $pConfId, " *, date_format(signWitness1DateTime,'%m-%d-%Y %h:%i %p') as signWitness1DateTimeFormat ");	
	}
	if(is_array($getPreOpQuesDetails)){
		extract($getPreOpQuesDetails);
		$preHealthQuestFormStatus = $form_status;
	}
	
	
	//allergies
	$allergy_lp = "Select * from patient_allergies_tbl where patient_confirmation_id='".$pConfId."'";
	$result_lp = imw_query($allergy_lp);
	$num_lp = @imw_num_rows($result_lp);	

	$getAllergiesName=array();
	$getAllergiesRect=array();
	$allergy1 = "Select pre_op_allergy_id,allergy_name,reaction_name from patient_allergies_tbl where patient_confirmation_id=$pConfId";
	$result = imw_query($allergy1);
	$num = imw_num_rows($result);	
	if($num >0) {
		$allergiesValueNKA = '__';
		while($rowAllergy = imw_fetch_array($result)) {
			$allergy1_rows[] = 	$rowAllergy;
			$getAllergiesName[$rowAllergy['pre_op_allergy_id']] = $rowAllergy['allergy_name'];
			$getAllergiesRect[$rowAllergy['pre_op_allergy_id']] = $rowAllergy['reaction_name'];
		}
		if($num==1 && trim(strtoupper($allergy1_rows[0]["allergy_name"]))=="NKA") {
			$allergiesValueNKA = 'Yes';
		}
	}else if($allergiesNKDA_status=="Yes") {
		$allergiesValueNKA = 'Yes';
	}else {
		$allergiesValueNKA = '__';
	}	
	
	if($preHealthQuestFormStatus=='completed' || $preHealthQuestFormStatus=='not completed'){
		$medication_health = "Select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_prescription_medication_healthquest_tbl where confirmation_id='".$pConfId."' ORDER BY prescription_medication_name";
		$medresult_health = imw_query($medication_health);
		$num_medhealth = @imw_num_rows($medresult_health);
$table_main="";
$table_main.="<page>".$head_table;	
$table_main.='<table style="width:700px;font-size:14px;border:1px solid #C0C0C0; margin-top:10px;" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" class="fheader bgcolor">Pre-Op Health Questionnaire</td>
				</tr>
				<tr>
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;font-weight:bold; padding-top:5px;">Have you ever had</td>
								<td style="width:50px;border-bottom:1px solid #C0C0C0;font-weight:bold;text-align:center; padding-top:5px;">Yes/No</td>
							</tr>
							<tr>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;">Heart Trouble/Heart Attack';
									if($heartTrouble=="Yes" && trim($heartTroubleDesc)){
										$table_main.='<br>
										<table style="width:250px; padding-top:5px; font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($heartTroubleDesc).'</td>
											</tr>
										</table>';
									}
								$table_main.='</td>
								<td style="width:50px;text-align:center;border-bottom:1px solid #C0C0C0; padding-top:5px;">';
								if($heartTrouble!=""){$table_main.=$heartTrouble;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">Stroke';
								if($stroke=="Yes" && trim($strokeDesc)){
									$table_main.='<br>
									<table style="width:250px; font-size:14px; padding-top:5px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
											<td style="width:180px;">'.stripslashes($strokeDesc).'</td>
										</tr>
									</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center; padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($stroke!=""){$table_main.=$stroke;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px; padding-top:5px;border-bottom:1px solid #C0C0C0;">HighBP';
								if($HighBP=="Yes" && trim($HighBPDesc)){
									$table_main.='<br><table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($HighBPDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($HighBP!=""){$table_main.=$HighBP;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Anticoagulation therapy (i.e. Blood Thinners)';
								if($anticoagulationTherapy=="Yes" && trim($anticoagulationTherapyDesc)){
								$table_main.='<br><table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($anticoagulationTherapyDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($anticoagulationTherapy!=""){$table_main.=$anticoagulationTherapy;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Asthma, Sleep Apnea, Breathing Problems';
									if($asthma=="Yes" && trim($asthmaDesc)){
										$table_main.='<br><table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($asthmaDesc).'</td>
											</tr>
										</table>';
									}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($asthma!=""){$table_main.=$asthma;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Tuberculosis';
								if($tuberculosis=="Yes" && trim($tuberculosisDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($tuberculosisDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($tuberculosis!=""){$table_main.=$tuberculosis;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Diabetes';
								if($insulinDependence){
									$table_main.='<br>';
									if($insulinDependence=="Yes"){
										$table_main.='<i><b>Insulin Dependent:</b>&nbsp;Yes</i>';
									}else if($insulinDependence=="No"){
										$table_main.='<i><b>Non-Insulin Dependent:</b>&nbsp;Yes</i>';	
									}
								}
								if($diabetes=="Yes" && trim($diabetesDesc)){
									$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.htmlentities(stripslashes($diabetesDesc)).'</td>
											</tr>
										</table>';
								}
							$table_main.='</td>
								<td style="width:50px;padding-top:5px;text-align:center;border-bottom:1px solid #C0C0C0;">';
								if($diabetes!=""){	$table_main.=$diabetes;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Epilepsy, Convulsions, Parkinson\'s, Vertigo';
								if($epilepsy=="Yes" && trim($epilepsyDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($epilepsyDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($epilepsy!=""){$table_main.=$epilepsy;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Restless Leg Syndrome';
								if($restlessLegSyndrome=="Yes" && trim($restlessLegSyndromeDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($restlessLegSyndromeDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($restlessLegSyndrome!=""){$table_main.=$restlessLegSyndrome;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
								$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Hepatitis';
								if($hepatitis=="Yes" && ($hepatitisA=='true' || $hepatitisB=='true' || $hepatitisC=='true')){
									$table_main.='<br>';
									if($hepatitisA=='true'){
										$table_main.='<b>A:</b>&nbsp;Yes';			
									}
									if($hepatitisB=='true'){
										$table_main.='&nbsp;<b>B:</b>&nbsp;Yes';			
									}
									if($hepatitisC=='true'){
										$table_main.='&nbsp;<b>C:</b>&nbsp;Yes';			
									}	
								}
								if($hepatitis=="Yes" && trim($hepatitisDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($hepatitisDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($hepatitis!=""){	$table_main.=$hepatitis;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Kidney Disease, Dialysis';
								
								if($kidneyDisease=="Yes" && trim($kidneyDiseaseDesc)){
								$table_main.='<br>
									<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">';
										if($shunt!=""){
											$table_main.='
											<tr>
												<td colspan="2"><b>Do you have a Shunt:</b>&nbsp;'.$shunt.'</td>
											</tr>	';
										}
										if($fistula!=""){
											$table_main.='
											<tr>
												<td colspan="2"><b>Fistula:</b>&nbsp;'.$fistula.'</td>
											</tr>';	
										}
										$table_main.='<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($kidneyDiseaseDesc).'</td>
											</tr>';
										
								$table_main.='</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($kidneyDisease!=""){	$table_main.=$kidneyDisease;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">HIV, Autoimmune Diseases';
								if($hivAutoimmuneDiseases=="Yes" && trim($hivTextArea)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($hivTextArea).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($hivAutoimmuneDiseases!=""){$table_main.=$hivAutoimmuneDiseases;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">History of cancer';
								$brestCancerLeftRight = '';
								if($cancerHistory=="Yes" &&$brestCancerLeft){
									if($brestCancerLeft=="Yes"){
										$brestCancerLeftRight = "Left";
									}else if($brestCancerLeft=="No"){
										$brestCancerLeftRight = "Right";
									}
									if($brestCancerLeft){
										$table_main.='<br><i><b>Breast Cancer:</b>&nbsp;'.$brestCancerLeftRight.'</i>';
									}
								}
								
								if($cancerHistory=="Yes" && trim($cancerHistoryDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($cancerHistoryDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($cancerHistory!=""){	$table_main.=$cancerHistory;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Organ Transplant';
								if($organTransplant=="Yes" && trim($organTransplantDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:50px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:200px;">'.stripslashes($organTransplantDesc).'</td>
											</tr>
										</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($organTransplant!=""){	$table_main.=$organTransplant;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">A Bad Reaction to Local or General Anesthesia';
								if($anesthesiaBadReaction=="Yes" && trim($anesthesiaBadReactionDesc)){
								$table_main.='<br>
										<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
												<td style="width:180px;">'.stripslashes($anesthesiaBadReactionDesc).'</td>
											</tr>';
										$table_main.='</table>';
								}
								$table_main.='</td>
								<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								if($anesthesiaBadReaction!=""){	$table_main.=$anesthesiaBadReaction;}else{$table_main.="____";}
								$table_main.='</td>
							</tr>';
							$table_main.='<tr>
								<td colspan="2" style="width:350px;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
								$table_main.='
										<table style="width:300px;padding-top:0px;font-size:14px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:50px;font-weight:bold;font-style:italic;">Other:&nbsp;</td>
												<td style="width:250px;">';
													if($otherTroubles!=""){	$table_main.=$otherTroubles;}else{$table_main.="____";}
								$table_main.='	</td>
											</tr>
										</table>
								</td>
							</tr>';
						$table_main.='</table>
					</td>			
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="2" style="width:360px; font-size:13px;"><b>Allergies/Drug Reaction&nbsp;NKA:</b>';
								$table_main.=$allergiesValueNKA;
								$table_main.='&nbsp;<b>Allergies Reviewed:</b>';
								if($allergies_status_reviewed){$table_main.=$allergies_status_reviewed;}else{$table_main.="___";}
								$table_main.='</td>
							</tr>
							<tr>
								<td style="width:175px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;border-top:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Name</td>
								<td style="width:150px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;border-top:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Reaction</td>
							</tr>';
								if($num>0){
									foreach($getAllergiesName as $key=> $allergiesName){
									$table_main.='
										<tr>
											<td style="width:175px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.stripslashes(htmlentities($allergiesName)).'</td>
											<td style="width:150px;padding:5px;border-bottom:1px solid #C0C0C0;">'.stripslashes(htmlentities($getAllergiesRect[$key])).'</td>
										</tr>';
									}
								}else{
									$table_main.='
										<tr>
											<td style="width:175px;padding:3px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:150px;padding:3px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>											
										</tr>
										<tr>
											<td style="width:175px;padding:3px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:150px;padding:3px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>											
										</tr>';
								}
									$table_main.='
									<tr>
										<td colspan="2" style="width:350px;">
											<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
												<tr>
													<td style="width:215px;padding-top:10px;"><strong>Take&nbsp;Prescription&nbsp;Medications</strong></td>
													<td style="width:135px;padding-top:10px;"><strong>No&nbsp;Medication:</strong>';
														if($noMedicationStatus == "Yes"){$table_main.="Yes";}else{$table_main.="___";}
									$table_main.='					
													</td>
												</tr>
												<tr>
													<td colspan="2" style="width:350px;padding-top:10px;"><strong>Comments:</strong>';
														if($noMedicationStatus == "Yes" && trim($noMedicationComments)){$table_main.=$noMedicationComments;}else{$table_main.="__________";}
									$table_main.='					
													</td>
												</tr>
											</table>
										</td>										
									</tr>
									
									<tr>
										<td colspan="2" style="width:350px;">
											<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
														<tr>
															<td style="width:125px;font-weight:bold;padding-top:5px;border-top:1px solid #C0C0C0;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Name</td>
															<td style="width:100px;font-weight:bold;padding-top:5px;border-top:1px solid #C0C0C0;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Dosage</td>
															<td style="width:100px;font-weight:bold;padding-top:5px;border-top:1px solid #C0C0C0;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Sig</td>
														</tr>';
												if($num_medhealth>0){
													while($fetchRows_medhealth=imw_fetch_assoc($medresult_health)){
													$table_main.='
														<tr>
															<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($fetchRows_medhealth['prescription_medication_name']).'</td>
															<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($fetchRows_medhealth['prescription_medication_desc']).'</td>
															<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($fetchRows_medhealth['prescription_medication_sig']).'</td>
														</tr>';														
													}
												}else {
													$table_main.='
														<tr>
															<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
															<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
															<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
														</tr>
														<tr>
															<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
															<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
															<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
														</tr>';
														
												}
										$table_main.='		
											</table>
										</td>
									</tr>';
									$table_main.='<tr>
										<td colspan="2" style="width:350px;">
											<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
												<tr>
													<td style="width:300px;border-bottom:1px solid #C0C0C0;font-weight:bold; padding-top:5px;">Do You</td>
													<td style="width:50px;border-bottom:1px solid #C0C0C0;font-weight:bold;text-align:center; padding-top:5px;">Yes/No</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Use a Wheel Chair, Walker or Cane';
													if($walker=="Yes" && trim($walkerDesc)){
													$table_main.='<br>
														<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
															<tr>
																<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:180px;">'.stripslashes($walkerDesc).'</td>
															</tr>
														</table>';
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($walker!=""){$table_main.=$walker;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Wear Contact lenses';
													if($contactLenses=="Yes" && trim($contactLensesDesc)){
													$table_main.='<br>
														<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
															<tr>
																<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:180px;">'.stripslashes($contactLensesDesc).'</td>
															</tr>
														</table>';
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($contactLenses!=""){	$table_main.=$contactLenses;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Smoke';
													if($smoke=="Yes" && trim($smokeHowMuch)){
													$table_main.='<br>														
															<b><i>How much:</i></b>&nbsp;'.stripslashes($smokeHowMuch);
													}
													if($smoke=="Yes"){
													
													$table_main.='<br>														
															<b><i>Patient advised not to smoke 24 H prior to surgery:</i></b>&nbsp;';
															if($smokeAdvise=="Yes"){	$table_main.=$smokeAdvise;}else{$table_main.="____";}
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($smoke!=""){	$table_main.=$smoke;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Drink Alcohol';
													if($smoke=="Yes" && trim($alchoholHowMuch)){
														$table_main.='<br><b><i>How much:</i></b>&nbsp;'.stripslashes($alchoholHowMuch);
													}
													if($alchohol=="Yes"){
													
													$table_main.='<br>														
															<b><i>Patient advised not to drink 24 H prior to surgery:</i></b>&nbsp;';
															if($alchoholAdvise=="Yes"){	$table_main.=$alchoholAdvise;}else{$table_main.="____";}
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($alchohol!=""){	$table_main.=$alchohol;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;border-bottom:1px solid #C0C0C0;">Have an automatic internal defibrillator';
													if($autoInternalDefibrillator=="Yes" && trim($autoInternalDefibrillatorDesc)){
													$table_main.='<br>
														<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
															<tr>
																<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:180px;">'.stripslashes($autoInternalDefibrillatorDesc).'</td>
															</tr>
														</table>';
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
													if($autoInternalDefibrillator!=""){	$table_main.=$autoInternalDefibrillator;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
												<tr>
													<td style="width:300px;padding-top:5px;">Have any Metal Prosthetics';
													if($metalProsthetics=="Yes" && trim($notes)){
													$table_main.='<br>
														<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
															<tr>
																<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>											<td style="width:180px;">'.stripslashes($notes).'</td>
															</tr>
														</table>';
													}
													$table_main.='</td>
													<td style="width:50px;text-align:center;padding-top:5px;">';
													if($metalProsthetics!=""){$table_main.=$metalProsthetics;}else{$table_main.="____";}
													$table_main.='</td>
												</tr>
											</table>
										   </td>
										</tr>';
						$table_main.='</table>
					</td>
				</tr>
			</table>';
		//adminHealthquestionare
	$selectAdminQuestionsQry="select * from healthquestioner";
	$selectAdminQuestions=imw_query($selectAdminQuestionsQry);
	$selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
	$inc=0;
	while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions)){
		foreach($ResultselectAdminQuestions as $key=>$value){
			$question[$i][$key]=$value;
		}
		$inc++;	
	}
	
	//echo $question[2]['question'];
	//End adminHealthquestionare
	$getQuesQry=imw_query("select * from healthquestionadmin where confirmation_id='$pConfId'");
	$k=0;
	$QuesnumRows=imw_num_rows($getQuesQry);
	while($getQuesRes=imw_fetch_array($getQuesQry)){
		foreach($getQuesRes as $key=>$val){
			$quest[$k][$key]=$val;
		}
		$k++;
	}
	if($QuesnumRows>0){
		$t = 0;
		$table_main.='<table style="width:700px; font-size:13px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0"><tr>';
		for($k=0;$k<ceil($QuesnumRows/2);$k++)
		{
		$i = $k;
		$quest[$k]['adminQuestion']; 
		$questionid[]=$quest[$k]['adminQuestionStatus'];
		$tr=0;								
		$endTd = $endTd<= $QuesnumRows ? $t + 2 : $QuesnumRows;
			for($t=$t;$t<$endTd;$t++){
					$table_main.='<td style="width:300px;padding-left:2px; padding-top:5px;border-bottom:1px solid #C0C0C0;">'.$quest[$t]['adminQuestion'];
					if($quest[$t]['adminQuestionStatus']=="Yes" && trim($quest[$t]['adminQuestionDesc'])){
						$table_main.='<br>
							<table style="width:250px;padding-top:5px;font-size:14px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:70px;font-weight:bold;font-style:italic;">Describe:&nbsp;</td>
									<td style="width:180px;">'.stripslashes($quest[$t]['adminQuestionDesc']).'</td>
								</tr>
							</table>';
					}
					$table_main.='</td>';
					if($t<$QuesnumRows){
						$table_main.='<td style="width:55px;text-align:center;border-right:1px solid #C0C0C0;padding-top:5px;border-bottom:1px solid #C0C0C0;">';
							if($quest[$t]['adminQuestionStatus']){
								$table_main.=$quest[$t]['adminQuestionStatus'];	
							}else{
								$table_main.="___";		
							}
						$table_main.='</td>';
						$tr++;	
					}
				if($tr%2==0){$table_main.='</tr><tr>';}	
			}
		}
		$table_main.='</tr></table>';
	}		

if($signNurseId<>0 && $signNurseId<>"") {
	$NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
	$signOnFileStatus = $signNurseStatus;	

}
//End CODE RELATED TO NURSE SIGNATURE ON FILE

//START CODE RELATED TO WITNESS SIGNATURE ON FILE
if($signWitness1Id<>0 && $signWitness1Id<>"") {
	$Witness1NameShow = $signWitness1LastName.", ".$signWitness1FirstName." ".$signWitness1MiddleName;
	$signOnFileWitness1Status = $signWitness1Status;	

}
//END CODE RELATED TO WITNESS SIGNATURE ON FILE

//image for signature


 $preid=$pConfId;
 $pertable = 'preophealthquestionnaire';
 $preidName = 'confirmation_id';
 $predocSign = 'patientSign';
 $predocSignwit = 'witnessSign';
$qry = "select patientSign from preophealthquestionnaire where confirmation_id = $pConfId";
$pixleResPat = imw_query($qry);
list($patientSign) = imw_fetch_array($pixleResPat);
require_once("imgGd.php");
drawOnImage($patientSign,$imgName,'patientSign.jpg');

$qry = "select witnessSign from preophealthquestionnaire where confirmation_id = $pConfId";
$pixleResWit = imw_query($qry);
list($witnessSign) = imw_fetch_array($pixleResWit);
require_once("imgGd.php");
drawOnImage($witnessSign,$imgName,'witnessSign.jpg');
//print_r(getAppletImage($preid,$pertable,$preidName,$predocSignwit,$signImage,$alt,"1234.jpg"));
/*******End of signature*****/

$table_main.='<table cellpadding="0" cellspacing="0" style="border:1px solid #C0C0C0;width:700px;padding:3px; font-size:14px;">';
				$table_main.='
					<tr>
						<td style="width:400px;"><b>Emergency Contact Person:</b>&nbsp;';
						if($emergencyContactPerson!=""){
							$table_main.=$emergencyContactPerson;
						}else{
							$table_main.="___________";
						}
								
					$table_main.='<br><b>Patient Signature:</b></td>
						<td style="width:250px;">';
							$table_main.='<b>Tel.</b>&nbsp;';
							if($emergencyContactPhone){
								$table_main.=$emergencyContactPhone;
							}else{
								$table_main.="___________";
							}
					$table_main.='<br><b>Witness Name:</b>&nbsp;';
					if($witnessname){
						$table_main.=$witnessname;
					}else{
						$table_main.="________";
					}
					$table_main.='</td>
					</tr>';
				$table_main.='
					<tr>
						<td style="width:450px;vertical-align:top;padding-top:5px;">'	;
						if($patient_sign_image_path){
							if(file_exists($patient_sign_image_path)){
								$table_main.='<img src="../'.$patient_sign_image_path.'" width="150" height="83">&nbsp;';
							}
						}else{
							$table_main.="________";
						}
						$table_main.='&nbsp;&nbsp;&nbsp;<b>Date:</b>&nbsp;';
						if($objManageData->changeDateMDY($dateQuestionnaire)!='00-00-0000'){
							$table_main.=$objManageData->changeDateMDY($dateQuestionnaire);
						}else{
							$table_main.="________";	
						}
						$table_main.='</td>';
						$table_main.='<td style="width:277px;vertical-align:top;padding-top:5px;">';
						
						if($Witness1NameShow!=""){
							$table_main.="<br><b>Witness:&nbsp;</b>".$Witness1NameShow;
							$table_main.="<br><b>Electronically Signed:&nbsp;</b>Yes";
							$table_main.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signWitness1DateTime);
						}else if(trim($witness_sign_image_path)!=""){
							$table_main.="<br><b>Witness Signature:&nbsp;</b>";
							$table_main.="<br><img alt='' src='../".$witness_sign_image_path."' style='width:150px; height:65px;'>";
						}else {
							$table_main.="<br><b>Witness:&nbsp;</b>________";
							$table_main.="<br><b>Electronically Signed:&nbsp;</b>________";
							$table_main.="<br><b>Signature Date:&nbsp;</b>________";
						}
						$table_main.='</td>
					</tr>';
							
				$table_main.='<tr >';
				if($NurseNameShow!=""){	
					$table_main.='<td valign="top"><b>Nurse:</b>&nbsp;'.$NurseNameShow.'</td>';
				}
				if($signOnFileStatus!=""){	
					$table_main.='<td valign="top"><b>Electronically Signed:</b>&nbsp;'.$signOnFileStatus.'</td>';
				}
				$table_main.='</tr>';
		$table_main.='</table></page>';
		$table.=$table_main;
	}	


//================================================================================================//
//=====================================H & P Clearance Record=====================================//
if(file_exists("history_physicial_clearance_pdf_content.php")){
	$table_main="";
	include_once("history_physicial_clearance_pdf_content.php");
	if($table_main){$table.='<page>'.$table_main.'</page>';}
}
//================================================================================================//


//=====================================Pre-Op Nursing record=====================================//
$contentFile	=	'pre_op_nursing_record_print_doc.php';
if(file_exists($contentFile))
{
	$table_main="";
	include_once($contentFile);	
	if($table_main){$table.='<page backbottom="5mm">'.$table_main.'</page>';}
}
	
//================================================================================================//


/************ Start Pre Nursing Aldrete Scoring System *******************/ 

//Getting Details From table
$scoringDetails	=	$objManageData->getRowRecord("pre_nurse_alderate", "confirmation_id", $pConfId);
if( $scoringDetails )
{
	
	$scoreID			=	$scoringDetails->id;
	$pointsDetail	=	$scoringDetails->points_detail ;
	$formStatus 	=	$scoringDetails->form_status ;
	
	$pointsDetailArr	=	explode(",",$pointsDetail) ;
	
}
// Getting Details From table
if($formStatus=='completed' || $formStatus=='not completed'){
$table .= '<page>'.$head_table;
$table.='
	<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="4" style="width:740px;" class="fheader">Pre-Op Aldrete Scoring System</td>
		</tr>
		<tr >
				<th class="bdrbtm bgcolor bold" width="80" height="35" align="center" valign="middle">Sr. no.</th>
				<th class="bdrbtm bgcolor bold" width="120" valign="middle">Category</th>
				<th class="bdrbtm bgcolor bold" width="380" valign="middle">Comment</th>
				<th class="bdrbtm bgcolor bold" width="150" align="center" valign="middle">Point(s) Earned</th>
		</tr>
		';
		
		$ScoringCategories		=	$objManageData->getArrayRecords('alderate_scoring_categories','','','id','ASC');
		
		if( is_array($ScoringCategories) && count($ScoringCategories) > 0 )
		{
			
			$TotalPoints		=	0	;
			$counter			=	0;
			foreach($ScoringCategories as $key=>$cats)
			{
					$table	.='
							<tr >
								<td class="bdrbtm " height="25" align="center" valign="middle">'.(++$counter).'</td>
								<td class="bdrbtm " valign="middle">'. $cats->categoryName .'</td>
								';
								
					
					$ScoringQuestions	=		$objManageData->getArrayRecords('alderate_scoring_questions', 'category_id', $cats->id, 'id', 'ASC' );  	
					
					if( is_array($ScoringQuestions) && count($ScoringQuestions) > 0 )
					{
							$points	=	'' ;
							$txt			=	'' ;
							foreach($ScoringQuestions as $key=>$question)
							{
									$NA	=	$question->category_id . '-NA';
									$val	=	$question->category_id . '-' . $question->id;
									if(in_array($NA,$pointsDetailArr))
									{
											$points	=	'N/A' ;
									}
									elseif(in_array($val,$pointsDetailArr))
									{
											$points		=	$question->assessment_point . ' Point(s)'	;
											$TotalPoints	+=	$question->assessment_point	;	
											$txt				=	$question->question ; 	
									}

									
							}
							
							$table	.='
											<td class="bdrbtm " valign="middle">'.$txt.'</td>
											<td class="bdrbtm " align="center" valign="middle">'.$points.'</td>
										';
										
					}
					else
					{
						
							$table.='
											<td class="bdrbtm ">&nbsp;</td>
											<td class="bdrbtm " >&nbsp;</td>
										';
										
					}
										
					$table .='</tr>';
					
			}
	
		}
		
$table.='			
		<tr >
				<th colspan="3" align="right">Total Point(s) Earned</th>
				<th height="40" align="center" valign="middle" >'.$TotalPoints.' Point(s)</th>
		</tr>
		
	</table>
		</page>
	';
}
/************ End Pre Nursing Aldrete Scoring System *******************/


$ViewPostopnursingQry = "select * from `postopnursingrecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewPostopnursingRes = imw_query($ViewPostopnursingQry) or die(imw_error()); 
		$ViewPostopnursingNumRow = @imw_num_rows($ViewPostopnursingRes);
		$ViewPostopnursingRow = imw_fetch_array($ViewPostopnursingRes); 
		
		$postOpSite = stripslashes($ViewPostopnursingRow["postOpSite"]);
		
		$postOpSiteTime = $objManageData->getTmFormat($ViewPostopnursingRow["postOpSiteTime"]);
		$hidd_postOpSiteTime = $ViewPostopnursingRow["postOpSiteTime"];
		$bs_na = $ViewPostopnursingRow["bs_na"];
		$bs_value = $ViewPostopnursingRow["bs_value"];
		$blood_sugar = ($bs_na) ? 'N/A' : $bs_value;
		$blood_sugar = ($blood_sugar) ? $blood_sugar : '___';
		$painLevelpost = $ViewPostopnursingRow["painLevel"];
		$nourishKind = stripslashes($ViewPostopnursingRow["nourishKind"]);
		$removedIntact = $ViewPostopnursingRow["removedIntact"];
		$heparinLockOutTime = $objManageData->getTmFormat($ViewPostopnursingRow["heparinLockOutTime"]);
		$heparinLockOutNA = $ViewPostopnursingRow["heparinLockOutNA"];
		$patient_aox3 = $ViewPostopnursingRow["patient_aox3"];
		$other_mental_status = stripslashes($ViewPostopnursingRow["other_mental_status"]);
		$recoveryComments = stripslashes($ViewPostopnursingRow["recoveryComments"]);
		$relivedNurseId = $ViewPostopnursingRow["relivedNurseId"];
		$patientReleased2Adult = $ViewPostopnursingRow["patientReleased2Adult"];
		$patientsRelation = $ViewPostopnursingRow["patientsRelation"];
		$patientsRelationOther = $ViewPostopnursingRow["patientsRelationOther"];
		$nurseId = $ViewPostopnursingRow["nurseId"]; 
		$nurseInitials = $ViewPostopnursingRow["nurseInitials"]; 
		$form_status  = $ViewPostopnursingRow["form_status"]; 
		$postNurseFormStatus = $ViewPostopnursingRow["form_status"];  
		$dischargeTime = $objManageData->getTmFormat($ViewPostopnursingRow['dischargeTime']);
		$patient_transfer = $ViewPostopnursingRow['patient_transfer'];
		$postNurseSignDateTime = $ViewPostopnursingRow["signNurseDateTime"]; 
		$signNurseId =  $ViewPostopnursingRow["signNurseId"];
		$signNurseFirstName =  $ViewPostopnursingRow["signNurseFirstName"];
		$signNurseMiddleName =  $ViewPostopnursingRow["signNurseMiddleName"];
		$signNurseLastName =  $ViewPostopnursingRow["signNurseLastName"]; 
		$signNurseStatus =  $ViewPostopnursingRow["signNurseStatus"];
		
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
		
		$version_num = $ViewPostopnursingRow['version_num'];
		//$signNurseDateTime =  $ViewPreopnursingRow["signNurseDateTime"];
									
		//GET NURSE ID FIRST TIME FROM PATIENT CONFIRMATION
			if($nurseId=="" || $nurseId==0) {
				$ViewNurseIdQry = "select * from `patientconfirmation` where  patientConfirmationId = '".$_REQUEST["pConfId"]."'";
				$ViewNurseIdRes = imw_query($ViewNurseIdQry) or die(imw_error()); 
				$ViewNurseIdRow = imw_fetch_array($ViewNurseIdRes); 
				$nurseId = $ViewNurseIdRow["nurseId"];
			}	
		//END GET NURSE ID FIRST TIME FROM PATIENT CONFIRMATION
	
		
		$NurseName = $userNameArr[$nurseId];
	//END GET NURSE NAME
	
	//TEMPRARY NAME OF NURSE
		if(trim($NurseName)=="") {
			$NurseName = "Nurse Name";
		}
	//END TEMPRARY NAME OF NURSE
		
	//CODE TO SET POSTOP SITE TIME
		if($postOpSiteTime=="00:00:00" || $postOpSiteTime=="") {
			//$hidd_postOpSiteTime = date("H:i:s");
			//$postOpSiteTime=date("h:i A");
			$postOpSiteTime="____";
		}
	//END CODE TO SET POSTOP SITE TIME
									
	//START CODE TO SET HEPARINLOCKOUT TIME
		if($heparinLockOutTime=="00:00:00" || $heparinLockOutTime=="") {
			$heparinLockOutTime = "____";
		}		
	//END CODE TO SET HEPARINLOCKOUT TIME	
	
	
//END VIEW RECORD FROM DATABASE

$ViewUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 

$loggedInUserName = $ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"];
$loggedInUserType = $ViewUserNameRow["user_type"];
$loggedInSignatureOfNurse = $ViewUserNameRow["signature"];

if($loggedInUserType<>"Nurse") {
	$loginUserName = $_SESSION['loginUserName'];
	$callJavaFun = "return noAuthorityFun('Nurse');";
//}else if ($loggedInUserType=="Nurse" && !$loggedInSignatureOfNurse) {
	//$callJavaFun = "return noSignInAdmin();";
}else {
	$loginUserId = $_SESSION["loginUserId"];
	$callJavaFun = "document.frm_post_op_nurse.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','post_op_nursing_record_ajaxSign.php','$loginUserId');";
   // $NurseNameShow = $loggedInUserName;
}

//$signOnFileStatus = "Yes";
$TDnurseNameIdDisplay = "block";
$TDnurseSignatureIdDisplay = "none";


if($signNurseId<>0 && $signNurseId<>"") {
	$NurseNameShowPostOp = $signNurseName;
	$signOnFileStatusPostOp = $signNurseStatus;	
	
	$TDnurseNameIdDisplay = "none";
	$TDnurseSignatureIdDisplay = "block";
}
$condArr					=	array();
$condArr['confirmation_id']	=	$_REQUEST["pConfId"];
$condArr['chartName']		=	'post_op_physician_order_form';
$pOrderData					=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$condArr,'physician_order_type="medication" DESC,recordId','Asc');
$pOrderStatus				=	(is_array($pOrderData) && count($pOrderData) > 0 ) ? 1	: 0 ;		

if($postNurseFormStatus=='completed' || $postNurseFormStatus=='not completed'){
	$table.="<page>".$head_table;
	$table.='<table style="width:700px; font-size:14px; border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2" style="font-size:15px; padding:10px 0 5px 0;text-decoration:underline; text-align:center; font-weight:bold;border-bottom:1px solid #C0C0C0;" class="bgcolor">Post-Op Nursing Record</td>
			</tr>
			
';
	
		if( $version_num > 1) {
			$table.='<tr><td colspan="2" style="width:700px;border-bottom:1px solid #C0C0C0; font-size:14px; padding-top:5px;">';
			$table.=	'<table style="width:100%; font-size:14px;" cellpadding="0" cellspacing="0">';
			$table.=		'<tr>';
			$table.=			'<td style="width:220px;padding:5px;border:1px solid #C0C0C0;"><b>Physician Orders/Medications&nbsp;</b></td>';
			$table.=			'<td style="width:200px;padding:5px;border:1px solid #C0C0C0;"><b>Time</b></td>';
			$table.=			'<td style="width:280px;padding:5px;border:1px solid #C0C0C0;"><b>Not Given</b></td>';
			$table.=		'</tr>';	
				if($pOrderStatus) {		
						foreach($pOrderData as $pOrderRow)
						{
							$time  =	($pOrderRow->physician_order_time <> '00:00:00') ? $objManageData->getTmFormat($pOrderRow->physician_order_time) : '' ;
							$table.=	'<tr>';
							$table.=		'<td style="width:220px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities(stripslashes($pOrderRow->physician_order_name)).'</td>';
							$table.=		'<td style="width:200px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.$time.'</td>';
							$table.=		'<td style="width:280px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.$pOrderRow->physician_order_not_given.'</td>';
							$table.=	'</tr>';	
						}
				}
				else
				{
					for($u = 0; $u < 3; $u++)
					{
						$table .= '<tr>
						<td style="width:180px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
						<td style="width:200px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
						<td style="width:320px;padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
						</tr>';	
					}
					
				}
				$table.=	'</table>';
				$table.='	</td>
				 </tr>';
		}
$ViewPostopNurseVitalSignQry = "select * from `vitalsign_tbl` where  confirmation_id = '".$_REQUEST["pConfId"]."' order by vitalsign_id";
	$ViewPostopNurseVitalSignRes = imw_query($ViewPostopNurseVitalSignQry) or die(imw_error()); 
	$ViewPostopNurseVitalSignNumRow = imw_num_rows($ViewPostopNurseVitalSignRes);
	if($ViewPostopNurseVitalSignNumRow>0) {
		$k=1;
		$table.='
					<tr>
						<td style="width:450px;padding:5px 0px 5px 0px;border-bottom:1px solid #C0C0C0;font-size:14px; font-weight:bold;">Recovery Vital Signs</td>
						<td style="width:150px;padding:5px 0px 5px 0px;border-bottom:1px solid #C0C0C0;font-weight:bold;">Pain Level:&nbsp;'.$painLevel.' </td>
					</tr>
			';		
		while($ViewPostopNurseVitalSignRow = imw_fetch_array($ViewPostopNurseVitalSignRes)) {
			$vitalsign_id=$ViewPostopNurseVitalSignRow["vitalsign_id"];  
			$vitalSignBp = $ViewPostopNurseVitalSignRow["vitalSignBp"];
			$vitalSignP = $ViewPostopNurseVitalSignRow["vitalSignP"];
			$vitalSignR = $ViewPostopNurseVitalSignRow["vitalSignR"];
			$vitalSignO2SAT = $ViewPostopNurseVitalSignRow["vitalSignO2SAT"];
			$vitalSignTime = $objManageData->getTmFormat($ViewPostopNurseVitalSignRow["vitalSignTime"]);
			$vitalSignTemp = $ViewPostopNurseVitalSignRow["vitalSignTemp"];
	
		$table.='<tr>
					<td colspan="2" style="width:700px;border-bottom:1px solid #C0C0C0; font-size:14px; padding-top:5px;">
						<table style="width:700px; " cellpadding="0" cellspacing="0"> 	
							<tr>
								<td style="width:100px;"><b>BP:</b>&nbsp;'.$vitalSignBp.'</td>
								<td style="width:90px;"><b>P:</b>&nbsp;'.$vitalSignP.'</td>
								<td style="width:90px;"><b>R:</b>&nbsp;'.$vitalSignR.'</td>
								<td style="width:100px;"><b>O2SAT:</b>&nbsp;'.$vitalSignO2SAT.'</td>
								<td style="width:120px;"><b>Time:</b>&nbsp;'.$vitalSignTime.'</td>
								<td style="width:150px;"><b>Temp:</b>&nbsp;'.$vitalSignTemp.'</td>
							</tr>
						</table>
					</td>
				</tr>
				';
		$k++;
		}
	}
	else
	{
		$table.='
			<tr>
				<td style="width:450px;padding:5px 0px 5px 0px;border-bottom:1px solid #C0C0C0;font-size:14px; font-weight:bold;">&nbsp;</td>
				<td style="width:150px;padding:5px 0px 5px 0px;border-bottom:1px solid #C0C0C0;font-weight:bold;">Pain Level:&nbsp;'.$painLevel.' </td>
			</tr>
			';		
		
	}
	$table.='<tr>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Post-Operative Site:</b>&nbsp;'.$postOpSite.'</td>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Time:</b>&nbsp;';
					if($postOpSiteTime){$table.=$postOpSiteTime;}else{$table.="_____";}
				$table.='</td>
			</tr>
			<tr>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;">
					<table style="width:550px; font-size:14px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:130px;vertical-align:top; font-weight:bold">Nourishment Kind:</td>
							<td style="width:370px;vertical-align:top;">';
							if($nourishKind){$table.=$nourishKind;}else{$table.="________________";}
							$table.='</td>
						</tr>
					</table>
				</td>
				<td style="padding-top:5px;border-bottom:1px solid #C0C0C0;">'.($version_num > 3 ? '<b>Blood Sugar:</b>&nbsp;'.$blood_sugar : '&nbsp;').'</td>
			</tr>
			<tr>
				<td style="padding-top:10px;font-size:15px;border-bottom:1px solid #C0C0C0;"><b>IV Discontinued&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Removed Intact/Pressure Dressing Applied:&nbsp;</b>';
				if($removedIntact=="Yes"){$table.=$removedIntact;}else{$table.="_____";}
				$table.='</td>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Time:</b>&nbsp;';
					if($heparinLockOutTime){$table.=$heparinLockOutTime;}else{$table.="_____";}
					if($heparinLockOutNA=="Yes"){$table.="&nbsp;&nbsp;&nbsp;<b>N/A:</b>&nbsp;Yes";}
				$table.='</td>
			</tr>
			<tr>
				<td colspan="2" style="width:700px;border-bottom:1px solid #C0C0C0; font-size:14px; padding-top:5px;">
					<table style="width:700px; " cellpadding="0" cellspacing="0"> 	
						<tr>
							<td style="width:250px;"><b>Patient Awake, Alert and Oriented times 3:</b>';
								if($patient_aox3=="Yes"){$table.=$patient_aox3;}else{$table.="_____";}
								$table.=
							'</td>
							<td style="width:250px;"><b>Patient Discharged To Home Via:</b>';
								if($patientReleased2Adult=="Yes"){$table.=$patientReleased2Adult;}else{$table.="_____";}
								$table.=
							'</td>
							<td style="width:200px;"><b>Relationship:</b>';
								if($patientsRelation && $patientsRelation!="other"){$table.=$patientsRelation;}
								else if($patientsRelationOther && $patientsRelation=="other"){$table.=$patientsRelationOther;}else{$table.="_____";}
								$table.=
							'</td>
						</tr>';
						if( $version_num > 2){
							$table.='<tr><td colspan="3" style="width:700px;"><b>Other Mental Status :</b>';
							if($other_mental_status){$table.=$other_mental_status;}else{$table.="_____";}
							$table.='</td></tr>';
						}
					$table.='
					</table>
				</td>
			</tr>';
			if( $version_num > 4){
			$table.='
			<tr>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Patient Transferred To Hospital:</b>&nbsp;';
					if($patient_transfer){$table.=$patient_transfer;}else{$table.="_____";}
				$table.='</td>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
			</tr>';
			}
			$table.='
			<tr>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Discharge Time:</b>&nbsp;';
					if($dischargeTime){$table.=$dischargeTime;}else{$table.="_____";}
				$table.='</td>
				<td style="padding-top:10px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>
			</tr>';
			$table.='
			<tr>
				<td colspan="2" class="bgcolor" style="width:700px;font-size:14px;font-weight:bold;">Surgery (OR)</td>
			</tr>
			
			<tr>
				<td colspan="2" style="width:700px;border-bottom:1px solid #C0C0C0;">
					<table  style="width:700px;" cellpadding="0" cellspacing="0">';
					$table.='
						<tr>
							<td style="width:230px;font-size:14px;border-bottom:1px solid #C0C0C0;padding-top:5px;"><b>In Room Time:</b> '.($surgeryTimeIn ? $surgeryTimeIn : "_____").'</td>
							<td style="width:230px;font-size:14px;border-bottom:1px solid #C0C0C0;padding-top:5px;"><b>Surgery Start Time:</b> '.($surgeryStartTime ? $surgeryStartTime : "_____").'</td>
							<td style="width:230px;font-size:14px;border-bottom:1px solid #C0C0C0;padding-top:5px;"><b>Surgery End Time:</b> '.($surgeryEndTime ? $surgeryEndTime : "_____").'</td>
						</tr>
						<tr>
							<td colspan="3" style="width:700px;font-size:14px;border-bottom:1px solid #C0C0C0;padding-top:5px;"><b>Out of Room:</b> '.($surgeryTimeOut ? $surgeryTimeOut : "_____").'</td>
						</tr>
					</table>
				</td>
			</tr>
			';
			//START CODE FOR NURSE POST OP CHECKLIST
			$postopNurseCheckListQry = "SELECT postOpNurseQuestionName,postOpNurseOption 
										FROM patient_postop_nurse_checklist 
										WHERE confirmation_id='".$_REQUEST['pConfId']."' ORDER BY `postOpNurseQuestionName`";
			$postopNurseCheckListRes = imw_query($postopNurseCheckListQry) or die(imw_error());
			$postopNurseCheckListNumRow = imw_num_rows($postopNurseCheckListRes);
			if($postopNurseCheckListNumRow>0) {
				$k=0;
				$table.='
				<tr>
					<td colspan="2" style="width:700px;">
						<table  style="width:100%;font-size:14px;" cellpadding="0" cellspacing="0">';
						$table.='
							<tr>
								<td class="bgcolor" style="width:300px;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">Nurse Post-Op Checklist</td>
								<td class="bgcolor" style="width:50px;text-align:right;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;padding-right:8px;">Yes/No</td>
								<td class="bgcolor" style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;">&nbsp;</td>
								<td class="bgcolor" style="width:50px;text-align:right;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;padding-right:8px;">Yes/No</td>
							</tr>
							<tr>';				
								while($postopNurseCheckListRow 	= imw_fetch_array($postopNurseCheckListRes)) {
									$k++;
									$postopNurseCheckListName 	= $postopNurseCheckListRow['postOpNurseQuestionName'];
									$postopNurseCheckListOption = $postopNurseCheckListRow['postOpNurseOption'];
									$table.='<td style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;">'.$postopNurseCheckListName.'</td>
											 <td style="width:50px;text-align:right;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0; padding-top:5px;padding-right:8px;">';
												if($postopNurseCheckListOption!=""){$table .= $postopNurseCheckListOption;}else{$table .= "____";}
									$table.='</td>';
									if(($k%2)!=0 && $k == $postopNurseCheckListNumRow) {
										//code for blank TD in the last
										$table.='<td style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;"></td><td style="width:50px;text-align:left;border-bottom:1px solid #C0C0C0; padding-top:5px;"></td>';		
									}
									if(($k%2)==0 && $k != $postopNurseCheckListNumRow) {
										$table.='</tr><tr>';		
									}
								}
					$table.='</tr>
						</table>
					</td>
				</tr>';
			}
			//END CODE FOR NURSE POST OP CHECKLIST
			
	$table.='<tr>
				<td colspan="2" style="padding-top:10px;border-bottom:1px solid #C0C0C0;"><b>Comments:</b>&nbsp;';
					if($recoveryComments){$table.=$recoveryComments;}else{$table.="________________";}
				$table.='</td>
			</tr>
			';
			
			
				
				
				
				
		


$qryQualityMeasures="SELECT qualityName,qualityStatus FROM qualitymeasures where  confirmation_id = '".$_REQUEST["pConfId"]."'";
$resQualityMeasures=imw_query($qryQualityMeasures);
$CheckNumRows=imw_num_rows($resQualityMeasures);
	$tdCtr=1;
	if($CheckNumRows>0){
		$table.='<tr>
					<td colspan="2" style="width:700px; ">
						<table style="width:700px; font-size:14px; border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">';
					$table.='<tr>
								<td style="width:300px;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">ASC Quality Control Measures</td>
								<td style="width:50px;text-align:center;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">Yes/No</td>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;padding-top:5px;">&nbsp;</td>
								<td style="width:50px;text-align:center;font-size:14px;font-weight:bold;border-bottom:1px solid #C0C0C0;padding-top:5px;">Yes/No</td>
							</tr>
						<tr>';
		while($getRowsQualityMeasures=imw_fetch_array($resQualityMeasures)){
			$getQualityName=$getRowsQualityMeasures['qualityName'];
			$getQualityStatus=$getRowsQualityMeasures['qualityStatus'];
			$table.='<td style="width:300px;padding:3px;border-bottom:1px solid #C0C0C0;">'.$getQualityName.'</td>';
			$table.='<td style="width:50px;padding:3px;text-align:center;border-bottom:1px solid #C0C0C0;"><b>';
			if($getQualityStatus!=""){$table.=$getQualityStatus;}else{$table.="___"; }
			$table.='</b></td>';
			if($tdCtr%2==0){ $table.='</tr><tr>'; }
			$tdCtr++;
			
		}
		$table.='</tr></table>
			</td>
		</tr>';
	}
	$table.='
		<tr>
			<td style="padding-top:10px;"><b>Nurse Signature:&nbsp;</b>&nbsp;';
			if($NurseNameShowPostOp){$table.=$NurseNameShowPostOp;}else{$table.="_______";}	
			if($relivedNurseId!=0 && $relivedNurseId!=""){
				$relivednurseQry="select lname,fname from users where usersId=".$relivedNurseId."";
				$relivednurseRec= imw_query($relivednurseQry);
				$relivednurseRes=imw_fetch_array($relivednurseRec);
				$relivedNurseName= $relivednurseRes['lname'].', '.$relivednurseRes['fname'];		
			}
	$table.='</td>
			<td style="padding-top:10px;"><b>Relief Nurse:&nbsp;</b>&nbsp;';
			if($relivednurseRes['lname'] && $relivednurseRes['fname']){$table.=$relivedNurseName;}else{$table.="______";}			
	$table.='</td>	
		</tr>
		<tr>
			<td colspan="2"><b>Electronically Signed:&nbsp;</b>&nbsp;';
			if($signOnFileStatusPostOp){$table.=$signOnFileStatusPostOp;}else{$table.="____";}			
	$table.='</td>
		</tr>
		<tr>
			<td colspan="2"><b>Signature Date:&nbsp;</b>&nbsp;';
			if($signOnFileStatusPostOp){$table.=$objManageData->getFullDtTmFormat($postNurseSignDateTime);}else{$table.="____";}			
	$table.='</td>
		</tr>';
	$table.='</table></page>';
} 
//===================================End Post Op Nursing Record================================//

/************ Start Post Nursing Aldrete Scoring System *******************/

//Getting Details From table
$scoringDetails	=	$objManageData->getRowRecord("post_nurse_alderate", "confirmation_id", $pConfId);
$scoringDetailsArr = $objManageData->getArrayRecords("post_nurse_alderate_data", "confirmation_id", $pConfId, 'created_on','ASC', ' AND is_deleted = 0 ');
if( $scoringDetails )
{
	
	//$scoreID			=	$scoringDetails->id;
	//$pointsDetail	=	$scoringDetails->points_detail ;
	$formStatus 	=	$scoringDetails->form_status ;
	$version_num 	=	$scoringDetails->version_num ;
	//$pointsDetailArr	=	explode(",",$pointsDetail) ;
	
}
// Getting Details From table
if($formStatus=='completed' || $formStatus=='not completed'){
$table .= '<page>'.$head_table;

//START CODE TO SHOW ASSESSMENT POINTS OF SAVED RECORD WITH DATE/TIME AND COMMENTS
$hSmry = '';
if($version_num > 1) {
	$assessmentPointsArr = array();
	$scoringQuest = $objManageData->getArrayRecords('alderate_scoring_questions', '', '', 'category_id,id', 'ASC' );  	
	if( is_array($scoringQuest) && count($scoringQuest) > 0 ) {
		foreach($scoringQuest as $scoringQuestObj) {
				$assessmentPointsArr[$scoringQuestObj->category_id.'-'.$scoringQuestObj->id] = $scoringQuestObj->assessment_point;
		}
	}
	
	if( is_array($scoringDetailsArr) && count($scoringDetailsArr) > 0 )
	{
		$hSmry.='<table style="width:710px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">';
		$hSmry.='	<tr>
						<td colspan="6" style="width:700px;" class="fheader">Post-Op Aldrete Scoring System</td>
					</tr>
					<tr>
						<th class="bdrbtm bold" colspan="6" height="30" valign="middle" style="background:#333;color:#FFF; padding-left:10px;width:710px;">Summary - Post-Op Aldrete Score</th>
					</tr>
					<tr >
						<th class="bdrbtm bgcolor bold" width="80" height="30" valign="middle" align="center">S. No.</th>
						<th class="bdrbtm bgcolor bold" width="100" valign="middle">Date</th>
						<th class="bdrbtm bgcolor bold" width="100" valign="middle">Time</th>
						<th class="bdrbtm bgcolor bold" width="80"  valign="middle">Score</th>
						<th class="bdrbtm bgcolor bold" width="160" valign="middle">Recorded By</th>
						<th class="bdrbtm bgcolor bold" width="180" valign="middle">Comments</th>
					</tr>';
		$c=0;
		foreach( $scoringDetailsArr as $h_data ) {
			$c++;
			$scoringDtTm 		= $objManageData->getFullDtTmFormat($h_data->created_on);
			list($scoringDt, $scoringTm, $scoringAmPm) = explode(' ',$scoringDtTm);
			$totalPointsEarned 	= 0;
			$pointsDtl 			= $h_data->points_detail;
			$pointsDtlArr 		= explode(',',$pointsDtl);
			if(count($pointsDtlArr)>0) {
				foreach($pointsDtlArr as $pointsDtlVal) {
					$totalPointsEarned += $assessmentPointsArr[$pointsDtlVal];
				}
			}
			$recordedByUsr 		= getUsrNm($h_data->created_by,true);
			$scoringComments 	= stripslashes($h_data->scoring_comments);
	
			$hSmry.='<tr >
						<td class="bdrbtm" width="80" height="30" valign="middle" align="center">'.$c.'</td>
						<td class="bdrbtm" width="100" valign="middle">'.$scoringDt.'</td>
						<td class="bdrbtm" width="100" valign="middle">'.trim($scoringTm.' '.$scoringAmPm).'</td>
						<td class="bdrbtm" width="80"  valign="middle">'.$totalPointsEarned.'</td>
						<td class="bdrbtm" width="160" valign="middle">'.$recordedByUsr.'</td>
						<td class="bdrbtm" width="180" valign="middle">'.$scoringComments.'</td>
					</tr>';
	
		}
		$hSmry.='</table>';
	}
	if(trim($hSmry)) {
		$table.=$hSmry."<br>";
	}
}
//END CODE TO SHOW ASSESSMENT POINTS OF SAVED RECORD WITH DATE/TIME AND COMMENTS


$table.='
	<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">';
if(!$hSmry) {
	$table.='			
		<tr>
			<td colspan="4" style="width:740px;" class="fheader">Post-Op Aldrete Scoring System</td>
		</tr>';
}
		
		$ScoringCategories		=	$objManageData->getArrayRecords('alderate_scoring_categories','','','id','ASC');
		
		if( is_array($scoringDetailsArr) && count($scoringDetailsArr) > 0 ) {
			$cntr = 0;
			foreach( $scoringDetailsArr as $data ) {
		
				$cntr++;
				$pointsDetail = $data->points_detail;
				$pointsDetailArr = explode(",",$pointsDetail);
		
				$record_details = '';
				$recorded_at = $objManageData->getFullDtTmFormat($data->created_on);
				$recorded_by = getUsrNm($data->created_by,true);
				if( $recorded_by && $recorded_at ) {
					$record_details = "Recorded by <b>".$recorded_by. '</b> on <b>'.$recorded_at."</b>&nbsp;";
				}
		
				$table.='
					<tr>
						<th class="bdrbtm bold" width="80" height="30" align="center" valign="middle" style="background:#333;color:#FFF;">'.$cntr.'</th>
						<th class="bdrbtm bold" width="650" colspan="3" height="30" style="text-align:right;background:#333;color:#FFF;" valign="middle">'.$record_details.'</th>
					</tr>
					<tr >
							<th class="bdrbtm bgcolor bold" width="80" height="30" align="center" valign="middle">S. No.</th>
							<th class="bdrbtm bgcolor bold" width="120" valign="middle">Category</th>
							<th class="bdrbtm bgcolor bold" width="380" valign="middle">Comment</th>
							<th class="bdrbtm bgcolor bold" width="150" align="center" valign="middle">Point(s) Earned</th>
					</tr>
					';

					if( is_array($ScoringCategories) && count($ScoringCategories) > 0 )
					{
						
						$TotalPoints		=	0	;
						$counter			=	0;
						foreach($ScoringCategories as $key=>$cats)
						{
								$table	.='
										<tr >
											<td class="bdrbtm " height="25" align="center" valign="middle">'.(++$counter).'</td>
											<td class="bdrbtm " valign="middle">'. $cats->categoryName .'</td>
											';
											
								
								$ScoringQuestions	=		$objManageData->getArrayRecords('alderate_scoring_questions', 'category_id', $cats->id, 'id', 'ASC' );  	
								
								if( is_array($ScoringQuestions) && count($ScoringQuestions) > 0 )
								{
										$points	=	'' ;
										$txt			=	'' ;
										foreach($ScoringQuestions as $key=>$question)
										{
												$val	=	$question->category_id . '-' . $question->id;
												
												if(in_array($val,$pointsDetailArr))
												{
														$points		=	$question->assessment_point . ' Point(s)'	;
														$TotalPoints	+=	$question->assessment_point	;	
														$txt				=	$question->question ; 	
												}
												
										}
										
										$table	.='
														<td class="bdrbtm " valign="middle">'.$txt.'</td>
														<td class="bdrbtm " align="center" valign="middle">'.$points.'</td>
													';
													
								}
								else
								{
									
										$table.='
														<td class="bdrbtm ">&nbsp;</td>
														<td class="bdrbtm " >&nbsp;</td>
													';
													
								}
													
								$table .='</tr>';
								
						}
				
					}
		
					$table.='			
					<tr >
							<th colspan="3" align="right">Total Point(s) Earned</th>
							<th height="40" align="center" valign="middle" >'.$TotalPoints.' Point(s)</th>
					</tr>';
				}
			}
$table.='		
	</table>
		</page>
	';
}
/************ End Post Nursing Aldrete Scoring System *******************/


 $patientdata=imw_query("select patientconfirmation.patientId,patientconfirmation.dos,
patientconfirmation.surgery_time,patient_data_tbl.patient_fname,patient_mname,
patient_data_tbl.patient_lname,patient_data_tbl.date_of_birth from patientconfirmation 
left join patient_data_tbl on patientconfirmation.ascId = patient_data_tbl.asc_id
where  patientconfirmation.patientConfirmationId='$pConfId'");
while($patient_data=imw_fetch_array($patientdata)){
   $dos= $patient_data['dos'];
   $surgerytime=$patient_data['surgery_time'];
   $patient_fname=$patient_data['patient_fname'];
   $patient_mname=$patient_data['patient_mname'];
   $patient_lname=$patient_data['patient_lname'];
   $dob=$patient_data['date_of_birth'];
}

 $stub_data=imw_query("select * from stub_tbl where dos='$dos' && patient_first_name ='$patient_fname' && patient_middle_name='$patient_mname'
&& patient_last_name='$patient_lname' && patient_dob='$dob' && surgery_time='$surgerytime'");

while($stubtbl_data=imw_fetch_array($stub_data)){
  $stub_id=$stubtbl_data['stub_id'];
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
	}else{
		$preopphy_patientConfirmSiteTemp = "Operative Eye";  //OU
	}
// END APPLYING NUMBERS TO PATIENT SITE


// GETTING IF PRE OP PHYSICIAN RECORD IS SAVED OR NOT
	$getPreOpPhyDetails = $objManageData->getRowRecord('preopphysicianorders', 'patient_confirmation_id', $pConfId);	
	if($getPreOpPhyDetails){
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
		
	}else{
	
		//GETTING SURGEON PROFILE TO SHOW FIRST VIEW "$surgeonId"
			unset($conditionArr);
			
			$conditionArr['surgeonId'] = $surgeonId;
			$conditionArr['del_status'] = '';
			$profilesDetail = $objManageData->getMultiChkArrayRecords('surgeonprofile', $conditionArr);
			if($profilesDetail){
				foreach($profilesDetail as $profile){
					$surgeonProfileId = $profile->surgeonProfileId;
					$proceduresList = $profile->procedures;
					$preOpOrder = $profile->preOpOrders;
					if(strpos($proceduresList, ", ")){
						$proceduresArray = explode(", ", $proceduresList);
						if(in_array(trim($patient_primary_procedure), $proceduresArray)){
							$procedureFound = 'true';
							break;
						}
					}else{
						if(trim($patient_primary_procedure)==trim($proceduresList)){
							$procedureFound = 'true';
							break;
						}
						$proceduresArray[] = $proceduresList;
					}
				}
			}	

			/*if($procedureFound=='true'){*/
				$profileIDToShow = $surgeonProfileId;
			/*}else{
				// SHOW DEFAULT PROFILE
					unset($conditionArr);
					$conditionArr['surgeonId'] = $surgeonId;
					$conditionArr['defaultProfile'] = '1';
					$defaultProfilesDetail = $objManageData->getMultiChkArrayRecords('surgeonprofile', $conditionArr);
					if(count($defaultProfilesDetail)>0){
						foreach($defaultProfilesDetail as $profileDetails){
							$profileIDToShow = $profileDetails->surgeonProfileId;
						}
					}
				// SHOW DEFAULT PROFILE
			}*/
			
			// PROFILE TO DISPLAY
				unset($conditionArr);
				$conditionArr['surgeonProfileId'] = $profileIDToShow;
				$showProfileDetails = $objManageData->getMultiChkArrayRecords('surgeonprofile', $conditionArr);
			// PROFILE TO DISPLAY
			
			// GETTING PRE-OP ORDERS MEDICATION NAMES
			if(count($showProfileDetails)>0){
				foreach($showProfileDetails as $profile){
					$proceduresList = $profile->procedures;
					$preOpOrders = $profile->preOpOrders;
					if(strpos($preOpOrders, ", ")){
						$preOpOrdersArr = explode(", ", $preOpOrders);
					}else{
						$preOpOrdersArr[] = $preOpOrders;
					}
				}
			}
			// GETTING PRE-OP ORDERS MEDICATION NAMES
			
		//GETTING SURGEON PROFILE TO SHOW FIEST VIEW "$surgeonId"
	}
// GETTING IF PRE OP PHYSICIAN RECORD IS SAVED OR NOT


//GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID
	
	$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' and del_status=''";
	$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
	//echo imw_num_rows($selectSurgeonRes);
	while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
		$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
	}
	if(is_array($surgeonProfileIdArr)){
		$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
	}else {
		$surgeonProfileIdImplode = 0;
	}
	$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
	$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
	$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
	//echo $selectSurgeonProcedureNumRow;
	if($selectSurgeonProcedureNumRow>0) {
		while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
			$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
			 
			if($patient_primary_procedure_id == $surgeonProfileProcedureId) {
				$surgeonProfileIdFound = $selectSurgeonProcedureRow['profileId'];
			}
		}
		/*if($surgeonProfileIdFound) {*/
			$selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonProfileId = '$surgeonProfileIdFound' and del_status=''";
		/*}else {	//ELSE SELECT DEFAULT PROFILE OF SURGOEN
			$selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonId = '$surgeonId' AND defaultProfile = '1'";
		}*/
		//if($surgeonProfileIdFound) {
			
			$selectSurgeonProfileFoundRes = imw_query($selectSurgeonProfileFoundQry) or die(imw_error());
			$selectSurgeonProfileFoundNumRow = imw_num_rows($selectSurgeonProfileFoundRes);
			if($selectSurgeonProfileFoundNumRow > 0) {
				$selectSurgeonProfileFoundRow = imw_fetch_array($selectSurgeonProfileFoundRes);
				$preOpOrdersFound = $selectSurgeonProfileFoundRow['preOpOrders'];
				$preOpOrdersFoundExplode = explode(',',$preOpOrdersFound);
			}
		//}
	}	
//GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID


//SHOW DETAIL OF PATIENT PRE OP MEDICATION
	$preOpPatientDetails = $objManageData->getArrayRecords('patientpreopmedication_tbl', 'patient_confirmation_id', $_REQUEST["pConfId"],'patientPreOpMediId','ASC');
//SHOW DETAIL OF PATIENT PRE OP MEDICATION


$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'preopphysicianorders' AND patient_conf_id = '$pConfId' ";
$rsNotes =imw_query($query_rsNotes);
$totalRows_rsNotes =imw_num_rows($rsNotes);
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
	if($prePhyFormStatus=='completed' || $prePhyFormStatus=='not completed'){
		$table_row="";
		$table_row.='<page>'.$head_table;
		$table_row.='
			<table cellpadding="0" cellspacing="0" style="width:700px; font-size:14px;border:1px solid #C0C0C0;margin-top:15px;">
		     	<tr>
					<td colspan="3" style="width:700px;text-align:center;padding-top:5px;padding-bottom:5px;text-decoration:underline;font-size:16px;font-weight:bold;">
						Pre-Op Physician Orders
					</td>
				</tr>';
				
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
					<td style="width:150px; text-align:center;height:20px;background:#C0C0C0;font-weight:bold;">Pre Op Orders</td>
					<td style="width:400px; text-align:center;height:20px; border-top:1px solid #C0C0C0;">On arrival the following drops will be given to the '.$preopphy_patientConfirmSiteTemp.'</td>					
					<td style="width:150px; text-align:center;height:20px;background:#C0C0C0;">&nbsp;</td>
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
							if(count($preOpPatientDetails)>0){
							foreach($preOpPatientDetails as $detailsOfMedication){
								$parientPreOpMediOrderId = $detailsOfMedication->patientPreOpMediId;
								$preDefined = $detailsOfMedication->medicationName;
								$strength = $detailsOfMedication->strength;
								$directions = $detailsOfMedication->direction;
								$timemeds[0] = $detailsOfMedication->timemeds;
								
								$timemeds[1] = $detailsOfMedication->timemeds1;
								$timemeds[2] = $detailsOfMedication->timemeds2;
								$timemeds[3] = $detailsOfMedication->timemeds3;
								$timemeds[4] = $detailsOfMedication->timemeds4;
								$timemeds[5] = $detailsOfMedication->timemeds5;
								$timemeds[6] = $detailsOfMedication->timemeds6;
								$timemeds[7] = $detailsOfMedication->timemeds7;
								$timemeds[8]= $detailsOfMedication->timemeds8;
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
							  <td style="padding:5px;width:'.$widthArr[0].'px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.htmlentities($preDefined).'</td>
							  <td style="padding:5px;width:'.$widthArr[1].'px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.htmlentities($strength).'</td>
							  <td style="padding:5px;width:'.$widthArr[2].'px; border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;vertical-align:top;">'.htmlentities($directions).'</td>';
							if($timeCol)
							{			 
								$table_row.='<td style="padding-left:3px;width:240px; border-bottom:1px solid #C0C0C0;vertical-align:top;">';		
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
								<table style="width:600px; font-size:14px;" cellpadding="0" cellspacing="0">
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
		/*else
		{ //Added this blank row to set width of columns when above fields not included in version no. 2 or greater
			$table_row.='<tr height="5"><td width="150">&nbsp;</td><td width="400">&nbsp;</td><td width="150">&nbsp;</td></tr>';
		}*/
						
		$table_row.='
						<tr>
							<td style="padding-top:5px;padding-bottom:5px;border-top:1px solid #C0C0C0;font-weight:bold;">
								Other Pre-Op Orders:&nbsp;
							</td>
							<td style="padding-top:5px;padding-bottom:5px;border-top:1px solid #C0C0C0;" colspan="2">';
							if($preOpOrdersOther){
								$table_row.=stripslashes($preOpOrdersOther);
							}
							$table_row.='</td>
						</tr>
						<tr>
							<td colspan="3" style="width:700px;border-top:1px solid #C0C0C0;padding-top:5px;">
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
									$table_row.='</td>';
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
					</table>
					</page>';
					$table.=$table_row;
	}
//=================End Pre Op Physician========================//

//=================Post Op Physician========================//
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

//GETTING CONFIRNATION DETAILS
	$getConfirmationDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if($getConfirmationDetails){
		extract($getConfirmationDetails);
	}
//GETTING CONFIRNATION DETAILS

// GETTING SURGEONS SIGN YES OR NO
	unset($conditionArr);
	$conditionArr['usersId'] = $surgeonId;
	$surgeonsDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if(count($surgeonsDetails)>0){
		foreach($surgeonsDetails as $usersDetail){
			$signatureOfSurgeon = $usersDetail->signature;
		}
	}
// GETTING SURGEONS SIGN YES OR NO

// GETTING NURSE SIGN YES OR NO
	unset($conditionArr);
	$conditionArr['usersId'] = $nurseId;
	$nurseDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr);	
	if(count($nurseDetails)>0){
		foreach($nurseDetails as $usersDetail){
			$signatureOfNurse = $usersDetail->signature;
		}
	}
// GETTING NURSE SIGN YES OR NO

$query_rsNotes = "SELECT * FROM eposted WHERE table_name = 'postopphysicianorders' AND patient_conf_id = '$pConfId' ";
$rsNotes =imw_query($query_rsNotes);
$totalRows_rsNotes =imw_num_rows($rsNotes);

include("common/pre_defined_popup.php");

//GETTING POST OP IS OR NOT
	$getPostOpDetails = $objManageData->getExtractRecord('postopphysicianorders', 'patient_confirmation_id', $pConfId);
	if($getPostOpDetails){
		extract($getPostOpDetails);
		$postPhyFormStatus = $form_status;
		//$postOpPhysicianOrdersId;
	}
//GETTING POST OP IS OR NOT

//START GET LOCAL ANES POST-OP SIGNATURE
$localAnesQry = "SELECT signAnesthesia3Id,signAnesthesia3FirstName,signAnesthesia3MiddleName,signAnesthesia3LastName,signAnesthesia3Status,signAnesthesia3DateTime, date_format(signAnesthesia3DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia3DateTimeFormat FROM localanesthesiarecord WHERE confirmation_id = '".$pConfId."'";
$localAnesRes =imw_query($localAnesQry);
if(imw_num_rows($localAnesRes)>0) {
	$localAnesRow 					= imw_fetch_array($localAnesRes);	
	$signAnesthesia3Id 				= $localAnesRow["signAnesthesia3Id"];
	$signAnesthesia3FirstName 		= $localAnesRow["signAnesthesia3FirstName"];
	$signAnesthesia3MiddleName 		= $localAnesRow["signAnesthesia3MiddleName"];
	$signAnesthesia3LastName 		= $localAnesRow["signAnesthesia3LastName"];
	$signAnesthesia3Status 			= $localAnesRow["signAnesthesia3Status"];
	$signAnesthesia3DateTime 		= $localAnesRow["signAnesthesia3DateTime"];
	$signAnesthesia3DateTimeFormat 	= $localAnesRow["signAnesthesia3DateTimeFormat"];
	if($signAnesthesia3Id<>0 && $signAnesthesia3Id<>"") {
		$Anesthesia3SubType = getUserSubTypeFun($signAnesthesia3Id); //FROM common/commonFunctions.php
	}
	$Anesthesia3PreFix = 'Dr.';
	if($Anesthesia3SubType=='CRNA') {
		$Anesthesia3PreFix = '';
	}				
}
//END GET LOCAL ANES POST-OP SIGNATURE

	$condArr		=	array();
	$condArr['confirmation_id']	=	$pConfId ;
	$condArr['chartName']			=	'post_op_physician_order_form' ;
	$pOrderData		=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$condArr,'recordId','Asc');
	$pOrderStatus		=	(is_array($pOrderData) && count($pOrderData) > 0 ) ? 1	: 0 ;		

if($postPhyFormStatus=='completed' || $postPhyFormStatus=='not completed'){
	$table_pdf='';
	$table_pdf.='<page>'.$head_table;
	$postphyOdrWidth = "220px;";
	$postphyOdrWidths = "10px;";
	$postphyOdrWidthz = "100px;";
	if($version_num < 2) {
		$postphyOdrWidth = "190px;";
		$postphyOdrWidths = "40px;";
		$postphyOdrWidthz = "100px;";
	}
	$table_pdf.='
	<table style="width:680px;font-size:14px;padding-top:10px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="3" style="width:680px;text-align:center;font-weight:bold;font-size:16px; text-decoration:underline;padding:5px 0px 5px 0px;">Post-Op Physician Orders</td>
		</tr>
		<tr>
			<td colspan="3" style="width:680px;background:#C0C0C0;font-weight:bold;height:20px;padding-left:5px;">Post Op Orders</td>
		</tr>
		<tr>
			<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Discharge Patient when</td>
			<td style="width:50px;padding:5px;font-weight:bold;border-bottom:1px solid #C0C0C0;">Done</td>
			<td style="width:330px;padding:5px;border-bottom:1px solid #C0C0C0;border-left:1px solid #C0C0C0;">';
			if($patientToTakeHome && !$pOrderStatus )
			{
				$table_pdf.=	'<b>Physician Orders&nbsp;</b>'.$physician_order_date_time ;
			}
			else
			{
				$table_pdf.='
				<table style="width:330px;font-size:14px; " cellpadding="0" cellspacing="0">
					<tr>		
						<td style="width:'.$postphyOdrWidth.'"><b>Physician Orders/Medications&nbsp;</b>'.(($patientToTakeHome && $pOrderStatus) ? $physician_order_date_time : '' ).'</td>';
				if($version_num < 2) {	
					$table_pdf.='
						<td style="width:'.$postphyOdrWidths.'"><b>Time</b></td>';
				}
				$table_pdf.='
						<td style="width:'.$postphyOdrWidthz.'" style="padding:10px;"><b>Order Type</b></td>
					</tr>
				</table>';
			}
$table_pdf.='
			</td>
			
		</tr>
		<tr>
			<td colspan="2" style="width:350px; vertical-align:top; border-left:solid 1px #C0C0C0; ">
				<table style="width:350px;font-size:14px; " cellpadding="0" cellspacing="0">
					<tr>
						<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Patient Assessed. Patient has recovered satisfactorily from sedation. This patient may be discharged after instructions given.</td>
						<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
				
						if($patientAssessed=='Yes'){
							$table_pdf.='Done';
						}else{
							$table_pdf.="____";
						}
						$table_pdf.='</td>
					</tr>
					<tr>
						<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Vital signs are stable</td>
						<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
				
						if($vitalSignStable=='Yes'){
							$table_pdf.='Done';
						}else{
							$table_pdf.="____";
						}
						$table_pdf.='</td>
					</tr>
					<tr>
						<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Post-Op Evaluation Completed</td>
						<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
						if($postOpEvalDone=='Yes'){
							$table_pdf.='Done';
						}else{
							$table_pdf.="____";
						}
						$table_pdf.='</td>
					</tr>
				</table>
			</td>
			<td style="width:330px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;vertical-align:top;">';
				//$table_pdf.=stripslashes($patientToTakeHome);
				$condArr		=	array();
				$condArr['confirmation_id']	=	$pConfId ;
				$condArr['chartName']			=	'post_op_physician_order_form' ;
				$pOrderData		=	$objManageData->getMultiChkArrayRecords('patient_physician_orders',$condArr,'recordId','Asc');
				
				if($pOrderStatus )
				{
					$table_pdf.=	'<table style="width:100%; font-size:14px;" cellpadding="0" cellspacing="0">';
					foreach($pOrderData as $pOrderRow)
					{
						$time	=	($pOrderRow->physician_order_time <> '00:00:00') ? $objManageData->getTmFormat($pOrderRow->physician_order_time) : '' ;
						$table_pdf.=	'<tr>';
						$table_pdf.=	'<td style="width:'.$postphyOdrWidth.' padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid">'.htmlentities(stripslashes($pOrderRow->physician_order_name)).'</td>';
						if($version_num < 2) {
							$table_pdf.='<td style="width:'.$postphyOdrWidths.' padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.$time.'</td>';
						}
						$table_pdf.='<td style="width:'.$postphyOdrWidthz.' padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.ucwords($pOrderRow->physician_order_type).'</td>';
						$table_pdf.=	'</tr>';	
					}
					$table_pdf.=	'</table>';
				}
				elseif($patientToTakeHome)
				{
					$pOrderData	=	explode(',',str_replace("\n",",",$patientToTakeHome	));
					
					$table_pdf.=	'<table style="width:330px;font-size:14px;" cellpadding="0" cellspacing="0">';	
					foreach($pOrderData as $pOrderRow)
					{
						$table_pdf.=	'<tr>';
						$table_pdf.=	'<td style="width:'.$postphyOdrWidth.' padding:5px;border-bottom:1px solid #C0C0C0;">'.$pOrderRow.'</td>';
						if($version_num < 2) {
							$table_pdf.='<td style="width:130px;padding:5px;border-bottom:1px solid #C0C0C0;">&nbsp;</td>';
						}
						$table_pdf.=	'</tr>';	
					}
					$table_pdf.=	'</table>';
				}
				
			$table_pdf.='</td>
		</tr>';
		if($version_num > 2) {
		$Nurse1NameShow = stripslashes($signNurse1LastName.', '.$signNurse1FirstName);
		$table_pdf.='
		<tr>
			<td colspan="3" style="width:700px;border-top:1px solid #C0C0C0;padding-top:5px;">
				<table style="width:700px; font-size:14px;" cellpadding="0">
					
					<tr>
						<td style="width:250px;" valign="top"><b>Post-Op orders noted by nurse:&nbsp;</b>
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
				</table>	
			</td>		
		</tr>';
		}
		$table_pdf.='				
		<tr>
			<td colspan="3" style="width:680px;background:#C0C0C0;font-weight:bold;height:20px;padding-left:5px;">Post Op Instruction Given</td>
		</tr>
		<tr>
			<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Written</td>
			<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
			 if($postOpInstructionMethodWritten=='Yes'){
				$table_pdf.='Done';
			}else{
				$table_pdf.="____";
			}
			$table_pdf.='</td>
			<td style="width:330px;padding:5px;padding-bottom:5px;border-bottom:1px solid #C0C0C0;border-left:1px solid #C0C0C0;"><b>Time:&nbsp;</b>';
			if(trim($postOpPhyTime)){
				$table_pdf.=$objManageData->getTmFormat(stripslashes($postOpPhyTime));
			}else{
				$table_pdf.="____";
			}
			$table_pdf.='</td>
		</tr>
		<tr>
			<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Verbal</td>
			<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
			if($postOpInstructionMethodVerbal=='Yes'){
				$table_pdf.='Done';
			}else{
				$table_pdf.="____";
			}
			$table_pdf.='</td>
			<td style="width:330px;padding:5px;padding-bottom:5px;border-bottom:1px solid #C0C0C0;border-left:1px solid #C0C0C0;">&nbsp;</td>
		</tr>
		<tr>
			<td style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;">Patient safely discharged from the center</td>
			<td style="width:50px;font-weight:bold;padding:5px;border-bottom:1px solid #C0C0C0;">';
			if($patientAccompaniedSafely=='Yes'){
				$table_pdf.='Done';
			}else{
				$table_pdf.="____";
			}
			$table_pdf.='</td>
			<td style="width:330px;padding:5px;padding-bottom:5px;border-bottom:1px solid #C0C0C0;border-left:1px solid #C0C0C0;">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" style="width:300px;padding:5px;border-bottom:1px solid #C0C0C0;"><b>Comments:&nbsp;</b>';
			if(trim($comment)){
				$table_pdf.=stripslashes($comment);
			}else{
				$table_pdf.="__________________";
			}
			
			$table_pdf.='</td>
		</tr>
		<tr>
			<td colspan="3" style="padding-top:10px; padding-left:5px; width:700px;">
				<table style="width:700px; font-size:14px;" cellpadding="0">
					<tr>
						<td style="width:250px;"><b>Surgeon:&nbsp;</b>';
							$signSurgeon="_________";
							if($signSurgeon1Status!='' && $signSurgeon1Status=="Yes"){
								$signSurgeon1Name= "Dr. ".$signSurgeon1LastName.','.$signSurgeon1FirstName;
								$table_pdf.=$signSurgeon1Name;
								$signSurgeon=$signSurgeon1Status;
							}else{
								$table_pdf.="_________";
							}
							$table_pdf.='<br><b>Electronically Signed:&nbsp;</b>'.$signSurgeon;
							$table_pdf.='<br><b>Signature Date:&nbsp;</b>';
							if($signSurgeon1DateTime!='' && $signSurgeon1Status=="Yes"){
								$table_pdf.=$objManageData->getFullDtTmFormat($signSurgeon1DateTime);
							}else{
								$table_pdf.="_________";
							}
						$table_pdf.='
						</td>
						<td style="width:250px;"><b>Nurse:&nbsp;</b>'; 
							$signNurse="_________";
							if($signNurseStatus=="Yes"){
							 	$signNurseName=$signNurseLastName.','.$signNurseFirstName;
								$table_pdf.=$signNurseName;
								$signNurse=$signNurseStatus;
							}else{
								$table_pdf.="_________";
							}
							$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>".$signNurse;
							$table_pdf.='<br><b>Signature Date:&nbsp;</b>';
							if($signNurseDateTime!='' && $signNurseStatus=="Yes"){
								$table_pdf.=$objManageData->getFullDtTmFormat($signNurseDateTime);
							}else{
								$table_pdf.="_________";
							}
			$table_pdf.='
						</td>
						<td style="width:200px;"><b>Relief Nurse:&nbsp;</b>'; 
							if($relivednurse!=''){
								$relivednursename=$userNameArr[$relivednurse];
								$table_pdf.=$relivednursename;
							}else{
								$table_pdf.="_________";
							}
			$table_pdf.='
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="padding-top:10px; padding-left:5px; width:700px;">
				<table style="width:700px; font-size:14px;" cellpadding="0">
					<tr>
						<td style="width:250px;">';
							if($signAnesthesia3Status=="Yes"){
								$table_pdf.="<b>Anesthesia Provider:&nbsp;</b>". " ".$Anesthesia3PreFix." ". $signAnesthesia3LastName.", ".$signAnesthesia3FirstName." ".$signAnesthesia3MiddleName;
								$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$table_pdf.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($signAnesthesia3DateTime);
							}else {
								$table_pdf.="<b>Anesthesia Provider:&nbsp;</b>________";
								$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>________";
								$table_pdf.="<br><b>Signature Date:&nbsp;</b>________";
							}
						$table_pdf.='
						</td>
					</tr>
				</table>
			</td>
		</tr>						
	</table>
	</page>';
	$table.=$table_pdf;
}
//===========================================================//

//=======================Local Anesthesia Record=====================================//


// GETTING CONFIRMATION DETAILS
	$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$finalizeStatus = $detailConfirmation->finalize_status;	
	$surgeonId = $detailConfirmation->surgeonId;
	$anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
	$confimDOS=$detailConfirmation->dos;
// GETTING CONFIRMATION DETAILS


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
		$time_split = explode(":",$MainTime);
		if($time_split[0]=='24') { //to correct previously saved records
			$MainTime = "12".":".$time_split[1].":".$time_split[2];
		}
		if($MainTime == "00:00:00") {
			$MainTime = "";
		}else {
			$MainTime = $objManageData->getTmFormat($MainTime);//date('h:iA',strtotime($MainTime));
			//$MainTime = date('h:iA',strtotime($MainTime));
			//$MainTime = substr($MainTime,0,-1);
		}
		return $MainTime;
	}	
//END FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION

	$detailConfirmationFinalize = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfId);
	$patient_primary_procedure = $detailConfirmationFinalize->patient_primary_procedure;
	$patient_secondary_procedure = $detailConfirmationFinalize->patient_secondary_procedure;
	$site = $detailConfirmationFinalize->site;

//GET DETAIL FROM PRE-OP NURSE RECORD
	$getPreOpNursingDetails = $objManageData->getRowRecord('preopnursingrecord', 'confirmation_id', $pConfId);
//GET DETAIL FROM PRE-OP NURSE RECORD
	
// GETTTING LOCAL ANES RECORD IF EXISTS
	$localAnesRecordDetails = $objManageData->getExtractRecord('localanesthesiarecord', 'confirmation_id', $pConfId, " *, date_format(signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia1DateTimeFormat , date_format(signAnesthesia2DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia2DateTimeFormat , date_format(signAnesthesia3DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia3DateTimeFormat, date_format(signAnesthesia4DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia4DateTimeFormat ");	
		$localanesFormStatus='';
		if($localAnesRecordDetails){
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
							$newSize = $objManageData->imageResize(680,400,500);						
							$priImageSize[0] = 500;
						}
											
						elseif($priImageSize[1] > 840){
							$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
							$priImageSize[1] = 600;
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
					if($time_split_apTime[0]=='24') { //to correct previously saved records
						$apTime = "12".":".$time_split_apTime[1].":".$time_split_apTime[2];
					}
					//$apTime = date('h:i A',strtotime($apTime));
					$apTime = $objManageData->getTmFormat($apTime);

					/*
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
					*/
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
	if($localanesFormStatus=='completed' || $localanesFormStatus=='not completed'){
		
		// Include Library Class file if form is saved once;
		include_once 'library/classes/local_anesthesia.php';
		$objLocalAnesData = new LocalAnesthesia;
		
		$table_print="";
		$table_print.="<page>".$head_table;
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
										<b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signAnesthesia4DateTime).'										
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
									<td class="bdrbtm" style="text-align:left;width:320px; ">Site Marked and Verified</td>
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
									<td colspan="2" style="width:180px" class="bdrbtm">';
									if($version_num > 5) {
										$table_print.='
										<b>Changes in H&amp;P documented:</b>&nbsp;';
										if($chartNotesReviewed=='Changed'){
											$table_print.="Done";
										}else{$table_print.="___";}
									}
									$table_print.='
									</td>
									<td style="width:200px;" class="bdrbtm">&nbsp;<b>Alert and Awake:</b>&nbsp;';
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
											if(count($getAllergiesName)>0){
												foreach($getAllergiesName as $keyA=> $allergiesNameAnes){
													$table_print.='
													<tr>
														<td style="width:200px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($allergiesNameAnes)).'</td>
														<td style="width:140px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($getAllergiesRect[$keyA])).'</td>
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
														<td style="width:125px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Name</td>
														<td style="width:100px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Dosage</td>
														<td style="width:100px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Sig</td>
													</tr>';
											$medsQry=("select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_anesthesia_medication_tbl where confirmation_id=$pConfId ORDER BY prescription_medication_name");
											$medsRes = imw_query($medsQry);
											$medsnum=@imw_num_rows($medsRes);
											if($medsnum>0){
												while($detailsMeds=@imw_fetch_array($medsRes)){
												$table_print.='
													<tr>
														<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_name']).'</td>
														<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_desc']).'</td>
														<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_sig']).'</td>
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
										$bp_p_rr_time=$objManageData->getTmFormat($bp_p_rr_time);
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
									<td colspan="2" style="width:300px;" class="bold bdrbtm">&nbsp;';
									$table_print.=
									'</td>
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
					
					$dosageArr	=	array('blank1','blank2', 'blank3','blank4','propofol','midazolam','ketamine','labetalol','Fentanyl','spo2','o2lpm');
	
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
										if($c!=10 && $c!=11){ $medLabel=htmlentities($medLabel);}
											$table_print.=
											'<tr>
												<td style="width:50px;border-right:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0; height:25px;font-weight:bold;vertical-align:middle;" >'.$medLabel."</td>";
										for($p=0;$p<20;$p++){
											$table_print.="<td style='width:25px; border-right:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0; height:25px;font-weight:bold;text-align:center;vertical-align:middle;'>".htmlentities($medArray[$p])."<br /><span style=\"font-size:8px; font-weight:normal; \">".htmlentities($t_medArray[$p])."</span></td>";
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
									</td>
									
											
										';
											$table_print.='
										
								</tr>
							</table>
						</td>
					</tr>';
			$table_print.='</table>
			</page><page>'.$page_footer_html;
			if($applet_data!='' || $grid_image_path){    
								$qry="select applet_data, applet_time_interval  from localanesthesiarecord where confirmation_id= $pConfId";
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
						<table style="width:480px;" cellpadding="0" cellspacing="0">
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
						$nurseName=	$userNameArr[$relivedIntraNurseId];						
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
					<td style="width:350px;border-right:1px solid #C0C0C0;:">
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
				</tr>';
			// Start Printing Additional Question
					
			$table_print .= $objLocalAnesData->mac_ques_print_html($pConfId);
		
			// End Printing Additional Question		
			$table_print.='		
				<tr>
					<td style="width:350px;" class="bdrbtm pl5">';
						if($signAnesthesia3Id<>0 && $signAnesthesia3Id<>"") {
							$Anesthesia3SubType = getUserSubTypeFun($signAnesthesia3Id); //FROM common/commonFunctions.php
						}
						$Anesthesia3PreFix = 'Dr.';
						if($Anesthesia3SubType=='CRNA') {
							$Anesthesia3PreFix = '';
						}				

					if($signAnesthesia3Status=="Yes"){
						$table_print.="<b>Anesthesia Provider:&nbsp;</b>". " ".$Anesthesia3PreFix." ". $signAnesthesia3LastName.", ".$signAnesthesia3FirstName." ".$signAnesthesia3MiddleName;
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
						$userTypeChk= $userTypeArr[$relivedPostNurseId];
						if($userTypeChk=="Nurse"){
							$userTypeLabel="Nurse";	
							$userTypeQry="Relief Nurse";
						}
						$nurseName2=$userNameArr[$relivedPostNurseId];
						
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
	$table_print.='</table></page>';
	$table.=$table_print;
	}
//==================================================================================//


//=======================Pre_op Geneneral anes record============================//
$table_main="";	
		$Insur_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
		$Insur_patientConfirm_tblRes = imw_query($Insur_patientConfirm_tblQry) or die(imw_error());
		$Insur_patientConfirm_tblRow = imw_fetch_array($Insur_patientConfirm_tblRes);
		$Insur_patientConfirmDosTemp = $Insur_patientConfirm_tblRow["dos"];
		$finalizeStatus = $Insur_patientConfirm_tblRow["finalize_status"];
		
		$preopgenAnesFormStatus='';
		$ViewPreopgenAnesQry = "select * from `preopgenanesthesiarecord` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
		$ViewPreopgenAnesRes = imw_query($ViewPreopgenAnesQry) or die(imw_error()); 
		$ViewPreopgenAnesNumRow = imw_num_rows($ViewPreopgenAnesRes);
		$ViewPreopgenAnesRow = imw_fetch_array($ViewPreopgenAnesRes); 
		
		$preopgenAnesFormStatus = $ViewPreopgenAnesRow["form_status"];
		$HeartProblem = $ViewPreopgenAnesRow["HeartProblem"];
		$HighBloodPressure = $ViewPreopgenAnesRow["HighBloodPressure"]; 
		$Stroke = $ViewPreopgenAnesRow["Stroke"];
		$Diabetes = $ViewPreopgenAnesRow["Diabetes"]; 
		$BleedingProblems = $ViewPreopgenAnesRow["BleedingProblems"];
		$AsthmaLungDisease = $ViewPreopgenAnesRow["AsthmaLungDisease"]; 
		$HiatalHernia = $ViewPreopgenAnesRow["HiatalHernia"];
		$LiverKidneyDisease = $ViewPreopgenAnesRow["LiverKidneyDisease"];
		$MotionSickness = $ViewPreopgenAnesRow["MotionSickness"]; 
		$ThyroidDisease = $ViewPreopgenAnesRow["ThyroidDisease"]; 
		$SeizuresFainting = $ViewPreopgenAnesRow["SeizuresFainting"]; 
		$NeurologicalDisease = $ViewPreopgenAnesRow["NeurologicalDisease"];
		$MentalDisease = $ViewPreopgenAnesRow["MentalDisease"]; 
		$medicalHistoryOther = $ViewPreopgenAnesRow["medicalHistoryOther"]; 
		
		$lastMenustrualPeriodTemp = $ViewPreopgenAnesRow["lastMenustrualPeriod"]; 
			$lastMenustrualPeriod_split = explode("-",$lastMenustrualPeriodTemp);
			$lastMenustrualPeriod = $lastMenustrualPeriod_split[1]."-".$lastMenustrualPeriod_split[2]."-".$lastMenustrualPeriod_split[0];
		
		$pregnantDueDateTemp = $ViewPreopgenAnesRow["pregnantDueDate"];
			$pregnantDueDate_split = explode("-",$pregnantDueDateTemp);
			$pregnantDueDate = $pregnantDueDate_split[1]."-".$pregnantDueDate_split[2]."-".$pregnantDueDate_split[0];
		
		$allergies2Medications = $ViewPreopgenAnesRow["allergies2Medications"]; 
		$current2Medications = $ViewPreopgenAnesRow["current2Medications"]; 
		$previousOperations = $ViewPreopgenAnesRow["previousOperations"]; 
		
		$probPrevAnesthesia = $ViewPreopgenAnesRow["probPrevAnesthesia"]; 
		$pnv = $ViewPreopgenAnesRow["pnv"];
		$dc = $ViewPreopgenAnesRow["dc"];
		$probPrevAnesthesiaDesc = $ViewPreopgenAnesRow["probPrevAnesthesiaDesc"];
		
		$familyHistoryAnesthesiaProblems = $ViewPreopgenAnesRow["familyHistoryAnesthesiaProblems"]; 
		$familyHistoryAnesthesiaProblemsDesc = $ViewPreopgenAnesRow["familyHistoryAnesthesiaProblemsDesc"];
		$smoke = $ViewPreopgenAnesRow["smoke"];
		$smokeCigarettes = $ViewPreopgenAnesRow["smokeCigarettes"];
		$smokeCigars = $ViewPreopgenAnesRow["smokeCigars"];
		$smokePipe = $ViewPreopgenAnesRow["smokePipe"];
		$smokePacks = $ViewPreopgenAnesRow["smokePacks"];
		$smokeYears = $ViewPreopgenAnesRow["smokeYears"];
		$smokeStopDateTemp = $ViewPreopgenAnesRow["smokeStopDate"];
			$smokeStopDate_split = explode("-",$smokeStopDateTemp);
			$smokeStopDate = $smokeStopDate_split[1]."-".$smokeStopDate_split[2]."-".$smokeStopDate_split[0];
		
		$alcohol = $ViewPreopgenAnesRow["alcohol"];
		$alcoholWeeksList = $ViewPreopgenAnesRow["alcoholWeeksList"];
		$alcoholNumber = $ViewPreopgenAnesRow["alcoholNumber"];
		$dentures = $ViewPreopgenAnesRow["dentures"];
		$cappedTeeth = $ViewPreopgenAnesRow["cappedTeeth"];
		$permanentBridge = $ViewPreopgenAnesRow["permanentBridge"];
		$looseBrokenTeeth = $ViewPreopgenAnesRow["looseBrokenTeeth"];
		$PeriodontalDisease = $ViewPreopgenAnesRow["PeriodontalDisease"];
		$otherDentalProblems = $ViewPreopgenAnesRow["otherDentalProblems"];
		$preOpComplications = $ViewPreopgenAnesRow["preOpComplications"];
		$whoUserType = $ViewPreopgenAnesRow["whoUserType"];
		$whoUserTypeLabel = ($whoUserType == 'Anesthesiologist') ? 'Anesthesia&nbsp;Provider' : $whoUserType ;
		$createdByUserId = $ViewPreopgenAnesRow["createdByUserId"];
		$relivedNurseId = $ViewPreopgenAnesRow["relivedNurseId"];
		$form_status = $ViewPreopgenAnesRow["form_status"];
		$ascId = $ViewPreopgenAnesRow["ascId"];
		$confirmation_id = $ViewPreopgenAnesRow["confirmation_id"];
		$patient_id = $ViewPreopgenAnesRow["patient_id"];

		
	
	
if($preopgenAnesFormStatus=='completed' || $preopgenAnesFormStatus=='not completed'){
	$table_main='';
	$table_main.='<page>'.$head_table;	
	$table_main.='<table style="width:700px;font-size:14px;border:1px solid #C0C0C0; margin-top:10px;" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" style="text-align:center;font-size:15px;font-weight:bold;padding:10px 0 5px 0;text-decoration:underline;">Pre-Op General Anesthesia Record</td>
				</tr>
				<tr>
					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;font-weight:bold; padding:5px;background:#C0C0C0;">Medical History</td>
								<td style="width:50px;border-bottom:1px solid #C0C0C0;font-weight:bold;text-align:center;padding-top:5px;background:#C0C0C0;">Yes/No</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;vertical-align:top;">Heart Problem</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px;vertical-align:top;text-align:center; ">';
								if($HeartProblem){$table_main.=$HeartProblem;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;padding-left:5px;vertical-align:top;">High Blood Pressure</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
								if($HighBloodPressure){$table_main.=$HighBloodPressure;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;vertical-align:top;">Stroke</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
								if($Stroke){$table_main.=$Stroke;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;vertical-align:top;">Diabetes</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
								if($Diabetes){$table_main.=$Diabetes;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
						</table>
					</td>

					<td style="width:350px;vertical-align:top;">
						<table style="width:350px; font-size:14px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:300px;border-bottom:1px solid #C0C0C0;font-weight:bold; padding:5px;background:#C0C0C0;">&nbsp;</td>
								<td style="width:50px;border-bottom:1px solid #C0C0C0;font-weight:bold;text-align:center; padding-top:5px;background:#C0C0C0;">Yes/No</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;vertical-align:top;">Bleeding Problems</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
								if($HeartProblem){$table_main.=$HeartProblem;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
							<tr>
								<td style="width:300px;height:15px;border-bottom:1px solid #C0C0C0; padding-top:5px;padding-left:5px;vertical-align:top;">Asthma Lung Disease</td>
								<td style="width:50px;height:15px;border-bottom:1px solid #C0C0C0;padding-top:5px; vertical-align:top;text-align:center; ">';
								if($HeartProblem){$table_main.=$HeartProblem;}else{$table_main.="___";}
								
								$table_main.='
								</td>
							</tr>
							<tr>
								
								<td colspan="2" style="width:350px;height:43px;border-bottom:1px solid #C0C0C0;padding-top:5px;padding-left:5px;vertical-align:top; "><span style="padding-right:5px;font-weight:bold;">Other:&nbsp;</span>';
								if(trim($medicalHistoryOther)){$table_main.=stripslashes($medicalHistoryOther);}else{$table_main.="________________________________________";}
								
								$table_main.='
								</td>
							</tr>
						</table>
					</td>					
				</tr>
				<tr>
					<td style="width:350px; vertical-align:top; ">
						<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="2" style="width:350px; background:#C0C0C0;padding:5px;"><b>Allergies to Medications</b>
								
								</td>
							</tr>
							<tr>
								<td style="width:175px;padding:5px;">Name</td>
								<td style="width:160px;padding:5px;border-right:1px solid #C0C0C0;">Reaction</td>
							</tr>';
							
							$table_main.='
							<tr>
								<td colspan="2" style="width:350px;verticle-align:top;padding-bottom:5px;">
									<table style=" width:350px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
								';
							//GETTING ALLERGIES REACTIONS TO DISPLAY
							if(@count($getAllergiesName)>0){
								foreach($getAllergiesName as $keyAG => $allergiesNameAG){
									$table_main.='
									<tr> 
										<td style="width:175px; padding:5px;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.stripslashes(htmlentities($allergiesNameAG)).'</td>
										<td style="width:150px; padding:5px; border-bottom:1px solid #C0C0C0;">'.stripslashes(htmlentities($getAllergiesRect[$keyAG])).'</td>
									</tr>';
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
							$table_main.='
									</table>
								</td>
							</tr>								
						</table>
					</td>';
					$medsQry=("select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_anesthesia_medication_tbl where confirmation_id=$pConfId ORDER BY prescription_medication_name");
					$medsRes=imw_query($medsQry);
					$medsnum=@imw_num_rows($medsRes);

					$table_main.='
					<td style="width:350px; vertical-align:top; ">
						<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="3" style="width:350px;font-weight:bold; background:#C0C0C0; height:20px;padding-left:5px;">Current Medications</td>
							</tr>
							<tr>
								<td style="width:125px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Name</td>
								<td style="width:100px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Dosage</td>
								<td style="width:100px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Sig</td>
							</tr>';
					if($medsnum>0){
						while($rowAllergies=imw_fetch_assoc($medsRes)){
						$table_main.='
							<tr>
								<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($rowAllergies['prescription_medication_name']).'</td>
								<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($rowAllergies['prescription_medication_desc']).'</td>
								<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($rowAllergies['prescription_medication_sig']).'</td>
							</tr>';														
						}
					}else {
						for($q=1;$q<=3;$q++) {
							$table_main.='
							<tr>
								<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
								<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
								<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
							</tr>';
						}
							
					}
			$table_main.='		
						</table>
					</td>
				</tr>
				<tr>
					<td style="width:350px; height:20px; text-align:center;font-weight:bold;background:#C0C0C0;">Problem w/Previous Anesthesia</td>
					<td style="width:350px; height:20px; text-align:center;font-weight:bold;background:#C0C0C0;">Family History Of Anesthesia Problems</td>
				</tr>
				<tr>
					<td style="width:350px;padding-bottom:5px;border-right:1px solid #C0C0C0;">
						<table style="width:350px;font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td rowspan="2" style="width:75px;height:40px;font-size:14px;font-weight:bold;text-align:center;">';
								if($probPrevAnesthesia=="Yes"){
									$table_main.="Yes";
								}else if($probPrevAnesthesia=="No"){
									$table_main.="None";	
								}else{
									$table_main.="None/Yes<br>____";		
								}
								$table_main.='
								</td>
								<td style="width:75px;height:20px;">PNV:&nbsp;';
								if($pnv=="Yes" && $probPrevAnesthesia=="Yes"){
									$table_main.="<b>Yes</b>";
								}else{
									$table_main.="___";
								}
								$table_main.='
								</td>
								<td rowspan="2" style="width:197px;height:40px; padding-left:5px;vertical-align:top">';
								if(trim($probPrevAnesthesiaDesc)!="" && $probPrevAnesthesia=="Yes"){
									$table_main.=$probPrevAnesthesiaDesc;
								}
								$table_main.='</td>
							</tr>
							<tr>
								
								<td style="width:75px;height:20px;">DC:&nbsp;&nbsp;&nbsp;';
								if($dc=="Yes"){
									$table_main.="<b>Yes</b>";
								}else{
									$table_main.="___";
								}
							
							$table_main.='
								</td>
							</tr>
						</table>
					</td>
					<td style="width:350px;padding-left:5px;border-left:1px solid #C0C0C0;padding-bottom:5px;">
						<table style="width:350px;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="height:40px; width:75px;vertical-align:middle;text-align:center;">';
								if($familyHistoryAnesthesiaProblems=="Yes"){
									$table_main.="<b>None</b>";	
								}else if($familyHistoryAnesthesiaProblems=="No"){
									$table_main.="<b>Yes</b>";	
								}else{
									$table_main.="<b>Yes/None</b>:&nbsp;___";
								}
								$table_main.='</td>
								<td style="height:40px; width:275px;vertical-align:top">';
								if(trim($familyHistoryAnesthesiaProblemsDesc)!=""){
									$table_main.=$familyHistoryAnesthesiaProblemsDesc;
								}
								$table_main.='</td>								
							</tr>
						</table>';
					$table_main.='</td>
				</tr>
				<tr>
					<td colspan="2" style="width:700px; height:20px; text-align:center;font-weight:bold;border-top:1px solid #C0C0C0;background:#C0C0C0;">Do You</td>
				</tr>
				<tr>
					<td colspan="2" style="width:700px; height:20px;padding-left:5px;padding-top:8px;"><b>Smoke:&nbsp;</b>';
					if($smoke=="No"){
						$table_main.="No";	
					}else{
						if($smoke=="" && $smokeCigarettes!=""){
							$table_main.="Yes";
						}else{
							$table_main.="Yes/No:&nbsp;__";
						}
						$table_main.="&nbsp;&nbsp;<b>Cigarettes:&nbsp;</b>".$smoke;
						if($smokeCigarettes=="No"){
							$table_main.="Yes";	
						}else{
							$table_main.="__";		
						}
						$table_main.="&nbsp;&nbsp;<b>Cigars:&nbsp;</b>";
						if($smokeCigars=="No"){
							$table_main.="Yes";	
						}else{
							$table_main.="__";		
						}
						$table_main.="&nbsp;&nbsp;<b>Pipe:&nbsp;</b>";
						if($smokePipe=="No"){
							$table_main.="Yes";	
						}else{
							$table_main.="__";		
						}
						$table_main.="&nbsp;&nbsp;<b>Packs/Day:&nbsp;</b>";
						if($smokePacks!=""){
							$table_main.=$smokePacks;	
						}else{
							$table_main.="__";		
						}
						$table_main.="&nbsp;&nbsp;<b>No. of Years:&nbsp;</b>";
						if($smokeYears!=""){
							$table_main.=$smokeYears;	
						}else{
							$table_main.="__";		
						}
						$table_main.="&nbsp;&nbsp;<b>If Stopped when:</b>";
						if($smokeStopDate!="" && $smokeStopDate!="--"){
							$table_main.=$smokeStopDate;	
						}else{
							$table_main.="___";		
						}
					}
					$table_main.='</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top:5px;padding-left:5px; height:20px; border-top:1px solid #C0C0C0;"><b>Alcoho:&nbsp;</b>';
					if(trim($alcohol)){
						$table_main.=$alcohol;
					}else{
						$table_main.="___";	
					}
					if(trim($alcohol)=="Yes"){
						$table_main.="<b>&nbsp;&nbsp;Drinks/Week:&nbsp;</b>";
						if(trim($alcoholWeeksList)){
							$table_main.=$alcoholWeeksList;
						}else{
							$table_main.="___";	
						}
					}
					$table_main.='</td>
				</tr>
				<tr>
					<td colspan="2" style="padding-top:5px;padding-left:5px; height:20px;width:700px; border-top:1px solid #C0C0C0;">
						<table style="width:700px;font-size:14px;" cellpadding="0" cellspacing="0">
							<tr>
								<td style="width:60px;vertical-align:top;"><b>Dental:</b></td>
								<td style="width:550px;line-height:1.3;"><b>Normal:&nbsp;</b>';
									if($permanentBridge=="Yes"){
										$table_main.=$permanentBridge;
									}else{
										$table_main.="___";	
									}
									$table_main.="&nbsp;&nbsp;<b>Dentures:&nbsp;</b>";
									if($dentures=="Yes"){
										$table_main.=$dentures;	
									}else{
										$table_main.="___";		
									}
									$table_main.="&nbsp;&nbsp;<b>Capped Teeth:&nbsp;</b>";
									if($cappedTeeth=="Yes"){
										$table_main.=$cappedTeeth;	
									}else{
										$table_main.="___";		
									}
									$table_main.="&nbsp;&nbsp;<b>Loose or Broken Teeth:&nbsp;</b>";
									if($looseBrokenTeeth=="Yes"){
										$table_main.=$cappedTeeth;	
									}else{
										$table_main.="___";		
									}
									$table_main.="<br><span style='padding-left:35px;'><b>Periodontal Disease:&nbsp;</b>";
									if($looseBrokenTeeth=="Yes"){
										$table_main.=$cappedTeeth;	
									}else{
										$table_main.="___";		
									}
									$table_main.="&nbsp;&nbsp;<b>Other:&nbsp;</b>";
									if(trim($otherDentalProblems)){
										$table_main.=$otherDentalProblems;	
									}else{
										$table_main.="______";		
									}
									$table_main.='</span>
								</td>
							</tr>		
						</table>
					</td>		
				</tr>
				<tr>
					<td colspan="2" style="width:700px; height:20px; padding-top:5px;border-top:1px solid #C0C0C0;">
						<b>Comments:&nbsp;</b>';
					if(trim($preOpComplications)){
						$table_main.=stripslashes($preOpComplications);
					}
					$table_main.='</td>
				</tr>';
				
					if($createdByUserId){
						$preOpGenUserName = $userNameArr[$createdByUserId];
					}
					
					if($relivedNurseId){
						$relivedNurseName = $userNameArr[$relivedNurseId];
					}
				$table_main.='
				<tr>
					<td colspan="2" style="width:700px;border-top:1px solid #C0C0C0;padding-top:10px;">
						<table style="width:700px; font-size:14px;" cellpadding="0">
							<tr>
								<td style="width:180px;"><b>Who:&nbsp;</b>';
									if($whoUserType){
										$table_main.=$whoUserTypeLabel;		
									}else{
										$table_main.="_________";			
									}
						$table_main.='
								</td>
								<td style="width:280px;"><b>Created By:&nbsp;</b>'; 
									if($preOpGenUserName){
										$table_main.=$preOpGenUserName;
									}else{
										$table_main.="_________";
									}
						$table_main.='
								</td>
								<td style="width:240px;"><b>Relief Nurse:&nbsp;</b>'; 
									if($relivedNurseName){
										$table_main.=$relivedNurseName;
									}else{
										$table_main.="_________";
									}
						$table_main.='
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</page>';
		$table.=$table_main;
	}
//==============================================================================//

//===========================General Anesthesia Record==========================//
		$selectPatientProcedureQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$selectPatientProcedureRes = imw_query($selectPatientProcedureQry) or die(imw_error());
	$selectPatientProcedureNumRow = @imw_num_rows($selectPatientProcedureRes);
	if($selectPatientProcedureNumRow>0) {
		$selectPatientProcedureRow = imw_fetch_array($selectPatientProcedureRes);
		$patient_primary_procedure = $selectPatientProcedureRow["patient_primary_procedure"];
		$patient_secondary_procedure = $selectPatientProcedureRow["patient_secondary_procedure"];
		
		$anesthesiologist_id = $selectPatientProcedureRow["anesthesiologist_id"];
		
		$getanesthesiologistNameQry = "select * from users where usersId='$anesthesiologist_id' and user_type='Anesthesiologist'";
		$getanesthesiologistNameRes = imw_query($getanesthesiologistNameQry) or die(imw_error());
		$getanesthesiologistNameRow = imw_fetch_array($getanesthesiologistNameRes); 
		$getanesthesiologistName = $getanesthesiologistNameRow["lname"].", ".$getanesthesiologistNameRow["fname"]." ".$getanesthesiologistNameRow["mname"];
		
		// GETTING NURSE SIGN OR NOT		
			$signatureOfAnestheologist = $getanesthesiologistNameRow["signature"];
		// GETTING NURSE SIGN OR NOT
		
	}
	
	
	
//END GET COMMON VARIABLES FOR THIS PAGE



	
	//SET FORM STATUS ACCORDING TO MANDATORY FIELD
		$form_status = "completed";
		
		 if($anesthesiaClass=="" || $armsTuckedLeft=="" || $armsTuckedRight==""
		 || $armsArmboardsLeft=="" || $armsArmboardsRight=="" || $eyeTapedLeft==""
		 || $eyeTapedRight=="" || $eyeLubedLeft=="" || $eyeLubedRight=="" || trim($evaluation)==""
		)
		
		{
			$form_status = "not completed";
		}
		
		
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	
	$chkgenAnesQry = "select * from `genanesthesiarecord` where  confirmation_id = '".$pConfId."'";
	$chkgenAnesRes = imw_query($chkgenAnesQry) or die(imw_error()); 
	$chkgenAnesNumRow = @imw_num_rows($chkgenAnesRes);
	
	//echo "<script>location.href='gen_anes_rec.php?formStatus=filled$saveLink';<script>";


//END SAVE RECORD TO DATABASE

//FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION
	/*function calculate_timeFun($MainTime) {
		$time_split = explode(":",$MainTime);
		if($time_split[0]>12) {
			$am_pm = " PM";
		}else {
			$am_pm = " AM";
		}
		if($time_split[0]>=13) {
			$time_split[0] = $time_split[0]-12;
			if(strlen($time_split[0]) == 1) {
				$time_split[0] = "0".$time_split[0];
			}
		}else {
			//DO NOTHNING
		}
		/*if($time_split[0]=="00") {
			$time_split[0] = 12; 
		}
		$MainTime = $time_split[0].":".$time_split[1].$am_pm;
		return $MainTime;
	}	*/
//END FUNCTION TO CALCULATE TIME FROM DATABASE AND DISPLAY IT IN APPLICATION

//VIEW RECORD FROM DATABASE
//	if($_POST['SaveRecordForm']==''){	
		$ViewgenAnesQry = "select *, date_format(signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia1DateTimeFormat, date_format(signAnesthesia2DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia2DateTimeFormat from `genanesthesiarecord` where  confirmation_id = '".$pConfId."'";
		$ViewgenAnesRes = imw_query($ViewgenAnesQry) or die(imw_error()); 
		$ViewgenAnesNumRow = @imw_num_rows($ViewgenAnesRes);
		$ViewgenAnesRow = imw_fetch_array($ViewgenAnesRes); 
		$genAnesFormStatus='';
		if($ViewgenAnesNumRow>0) {
			$genAnesFormStatus = $ViewgenAnesRow["form_status"];
			$alertOriented = $ViewgenAnesRow["alertOriented"];
			$assistedByTranslator = $ViewgenAnesRow["assistedByTranslator"];
			$bp = $ViewgenAnesRow["bp"];
			$P = $ViewgenAnesRow["P"];
			$rr = $ViewgenAnesRow["rr"];
			$sao = $ViewgenAnesRow["sao"];
			$patientVerified = $ViewgenAnesRow["patientVerified"];
			$BMIvalue = stripslashes(trim($ViewgenAnesRow["BMIvalue"]));
			$anesthesiaClass = $ViewgenAnesRow["anesthesiaClass"];
			$o2n2oavailable = $ViewgenAnesRow["o2n2oavailable"];
			$PatientReassessed = $ViewgenAnesRow["PatientReassessed"];
			$MachineEquipment = $ViewgenAnesRow["MachineEquipment"];
			$reserveTanksChecked = $ViewgenAnesRow["reserveTanksChecked"];
			$positivePressureAvailable = $ViewgenAnesRow["positivePressureAvailable"];
			$maskTubingPresent = $ViewgenAnesRow["maskTubingPresent"];
			$vaporizorFilled = $ViewgenAnesRow["vaporizorFilled"];
			$absorberFunctional = $ViewgenAnesRow["absorberFunctional"];
			$gasEvacuatorFunctional = $ViewgenAnesRow["gasEvacuatorFunctional"];
			$o2AnalyzerFunctional = $ViewgenAnesRow["o2AnalyzerFunctional"];
			$ekgMonitor = $ViewgenAnesRow["ekgMonitor"];
			$endoTubes = $ViewgenAnesRow["endoTubes"];
			$laryngoscopeBlades = $ViewgenAnesRow["laryngoscopeBlades"];
			$others = $ViewgenAnesRow["others"];
			$othersDesc = $ViewgenAnesRow["othersDesc"];
			
			$startTime = $ViewgenAnesRow["startTime"];
			/*
			if($startTime=="00:00:00" || $startTime=="") {
				//$startTime=date("H:i A");
				$startTime="";
			}else {
			*/
				$startTime=calculate_timeFun($startTime); //CODE TO DISPLAY START TIME
			//}
			
			$stopTime = $ViewgenAnesRow["stopTime"];
			/*
			if($stopTime=="00:00:00" || $stopTime=="") {
				//$stopTime=date("H:i A");
				$stopTime="";
			}else {
			*/
				$stopTime=calculate_timeFun($stopTime); //CODE TO DISPLAY STOP TIME
			//}
			
			$mac = $ViewgenAnesRow["mac"];
			$macValue = $ViewgenAnesRow["macValue"];
			$millar = $ViewgenAnesRow["millar"];
			$millarValue = $ViewgenAnesRow["millarValue"];
			$etTube = $ViewgenAnesRow["etTube"];
			$etTubeSize = $ViewgenAnesRow["etTubeSize"];
			$lma = $ViewgenAnesRow["lma"];
			$lmaSize = $ViewgenAnesRow["lmaSize"];
			$mask = $ViewgenAnesRow["mask"];
			
			$teethUnchanged = $ViewgenAnesRow["teethUnchanged"];
			$Monitor_ekg = $ViewgenAnesRow["Monitor_ekg"];
			$Monitor_etco2 = $ViewgenAnesRow["Monitor_etco2"];
			$Monitor_etco2Sat = $ViewgenAnesRow["Monitor_etco2Sat"];
			$Monitor_o2Temp = $ViewgenAnesRow["Monitor_o2Temp"];
			$Monitor_PNS = $ViewgenAnesRow["Monitor_PNS"];
			
			
			$genAnesSignApplet = $ViewgenAnesRow["genAnesSignApplet"];
			if($genAnesSignApplet!='')
{
	
 include_once("imageSc/imgGd.php");
		$qry="select genAnesSignApplet from genanesthesiarecord where confirmation_id= $pConfId";
		$qryRes = @imw_query($qry);
		$arrayRes = @imw_fetch_array($qryRes);
		$appletData= $arrayRes['genAnesSignApplet'];
		$imgName="bgGridBig.jpg";
		//$pixels = "TUp:138,33;CDr:193,42;CFill:290,229;96:458,400;23:202,126;96:362,254;1.5:154,400;0.5:218,400;0:250,400;ram:330,318;36:138,512;SB:138,544;22:154,558;mg Succinylchol..:41,543;Helo world:41,558;TUp:251,436;Cr:268,452;CDr:284,468;CFill:300,483;TDn:315,500;96:250,578;63:330,594;ST:282,610;100:346,624;66:394,640;1.5:330,384;7.5:378,416;96:458,142;36:314,126;12:186,206;00:602,14;36:138,14;66:138,366;36:602,366;TDn:349,5;TDn:443,356;CFill:203,354;CFill:219,354;CFill:395,3;CFill:604,148;CFill:140,132;CDr:524,355;CDr:139,212;CDr:604,212;CDr:284,3;TUp:333,354;TUp:491,2;TUp:604,243;TUp:140,275;";	
		//drawOnImage($pixels,$imgName,"tess.jpg");	
		drawOnImage2($appletData,$imgName,"new_html2pdf/tess3.jpg","","new_html2pdf/");			
}	
/*******end function************/
			
			$armsTuckedLeft = $ViewgenAnesRow["armsTuckedLeft"];
			$armsTuckedRight = $ViewgenAnesRow["armsTuckedRight"];
			$armsArmboardsLeft = $ViewgenAnesRow["armsArmboardsLeft"];
			$armsArmboardsRight = $ViewgenAnesRow["armsArmboardsRight"];
			$eyeTapedLeft = $ViewgenAnesRow["eyeTapedLeft"];
			//$eyeTapedRight = $ViewgenAnesRow["eyeTapedRight"];
			$eyeLubedLeft = $ViewgenAnesRow["eyeLubedLeft"];
			//$eyeLubedRight = $ViewgenAnesRow["eyeLubedRight"];
		
			$pressurePointsPadded = $ViewgenAnesRow["pressurePointsPadded"];
			
			$bss = $ViewgenAnesRow["bss"];
			$warning = $ViewgenAnesRow["warning"];
			
			$temp = $ViewgenAnesRow["temp"];
			$StableCardioRespiratory = $ViewgenAnesRow["StableCardioRespiratory"];
			$graphComments = $ViewgenAnesRow["graphComments"];
			$evaluation = $ViewgenAnesRow["evaluation"];
			$comments = $ViewgenAnesRow["comments"];
			$anesthesiologistId = $ViewgenAnesRow["anesthesiologistId"];
			$anesthesiologistSign = $ViewgenAnesRow["anesthesiologistSign"];
			$relivedNurseId = $ViewgenAnesRow["relivedNurseId"];
			
			$signAnesthesia1Id =  $ViewgenAnesRow["signAnesthesia1Id"];
			$signAnesthesia1FirstName =  $ViewgenAnesRow["signAnesthesia1FirstName"];
			$signAnesthesia1MiddleName =  $ViewgenAnesRow["signAnesthesia1MiddleName"];
			$signAnesthesia1LastName =  $ViewgenAnesRow["signAnesthesia1LastName"];
			$signAnesthesia1Name= $ViewgenAnesRow["signAnesthesia1LastName"].','.$ViewgenAnesRow["signAnesthesia1FirstName"];
			$signAnesthesia1Status =  $ViewgenAnesRow["signAnesthesia1Status"];
			
			$signAnesthesia2Id 					=  $ViewgenAnesRow["signAnesthesia2Id"];
			$signAnesthesia2DateTimeFormat 	= $ViewgenAnesRow["signAnesthesia2DateTimeFormat"];
			$signAnesthesia2FirstName 	=  $ViewgenAnesRow["signAnesthesia2FirstName"];
			$signAnesthesia2MiddleName 	=  $ViewgenAnesRow["signAnesthesia2MiddleName"];
			$signAnesthesia2LastName 		=  $ViewgenAnesRow["signAnesthesia2LastName"]; 
			$signAnesthesia2Status 			=  $ViewgenAnesRow["signAnesthesia2Status"];
			
			$reliefNurseId					= $ViewgenAnesRow["reliefNurseId"];
			$confirmIPPSC_signin		= $ViewgenAnesRow["confirmIPPSC_signin"];
			$siteMarked 						= $ViewgenAnesRow["siteMarked"];
			$patientAllergies 			= $ViewgenAnesRow["patientAllergies"];
			$difficultAirway				= $ViewgenAnesRow["difficultAirway"];
			$anesthesiaSafety				= $ViewgenAnesRow["anesthesiaSafety"];
			$allMembersTeam 				= $ViewgenAnesRow["allMembersTeam"];
			$riskBloodLoss 					= $ViewgenAnesRow["riskBloodLoss"];
			$bloodLossUnits					=	$ViewgenAnesRow["bloodLossUnits"];
			
			$vitalSignGridStatus		=	$ViewgenAnesRow["vitalSignGridStatus"];
			$version_num						=	$ViewgenAnesRow["version_num"];
			
			$ascId = $ViewgenAnesRow["ascId"];
			$confirmation_id = $ViewgenAnesRow["confirmation_id"];
			$patient_id = $ViewgenAnesRow["patient_id"];
			$elem_cnvs_anes_drw_file				= $ViewgenAnesRow["drawing_path"];
			$elem_cnvs_anes_drw_coords				= $ViewgenAnesRow["drawing_coords"];
		//}
	}
	
	//GET PATIENT HEIGHT AND WEIGHT FROM PREOP NURSING RECORD
		$selectPreOpNursingQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectPreOpNursingRes = imw_query($selectPreOpNursingQry) or die(imw_error());
		$selectPreOpNursingNumRow = @imw_num_rows($selectPreOpNursingRes);
		if($selectPreOpNursingNumRow>0) {
			$selectPreOpNursingRow = imw_fetch_array($selectPreOpNursingRes);
			$patientHeight = stripslashes($selectPreOpNursingRow["patientHeight"]);
			
			if(trim($patientHeight)<>"" || $patientHeight<>"") {
				$patientHeightsplit = explode("'",$patientHeight); 
				$patientHeight = $patientHeightsplit[0]."' ".$patientHeightsplit[1].'"';
				
				$patientHeightInches = ($patientHeightsplit[0]*12)+$patientHeightsplit[1];
			}else {
				$patientHeight = "";
			}
			$patientWeight = $selectPreOpNursingRow["patientWeight"];
			if($patientWeight<>"") {
				$patientWeight = $patientWeight." lbs";
			}
			//CODE TO CALCULATE BMI VALUE
				if(!$BMIvalue) {
					if((trim($patientHeight)<>"" || $patientHeight<>"") && $patientWeight<>"") {
						
						$BMIvalueTemp = $patientWeight * 703/($patientHeightInches*$patientHeightInches);
						$BMIvalue = number_format($BMIvalueTemp,2,".","");
					}
				}	
			//END CODE TO CALCULATE BMI VALUE
		}
if($genAnesFormStatus=='completed' || $genAnesFormStatus=='not completed'){
	$table_pdf='';
	$table_pdf.='<page>'.$head_table;
	$table_pdf.="<style>.bdrbtm{vertical-align:middle;}</style>";
	$table_pdf.='<table style="width:744px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
  					<tr>
						<td colspan="4" class="fheader" style="width:744px;" >General Anesthesia Record</td>
					</tr>';
					
					if($version_num > 1) 
					{
					$table_pdf.='
					<tr>
						<td colspan="4" style="width:744px;" >
							<table style="width:744px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:744px;" class="bold bdrtop bgcolor">The following items were verified before Induction of Anesthesia</td>
								</tr>
								<tr>	
									<td style="width:740px;" valign="middle">
										<table style="width:700px; font-size:13px;" cellspacing="0" cellpadding="0">
											<tr>
												<td style="width:280px;" valign="top">
													<strong>Nurse and anesthesia care provider confirm:&nbsp;</strong>
												</td>
												<td style="width:200px; vertical-align:top;text-align:left;">';
												if($signAnesthesia2Id)
												{
													$table_pdf.='<b>Anethesia Provider:</b> '.$signAnesthesia2LastName.", ".$signAnesthesia2FirstName." ".$signAnesthesia2MiddleName."<br>
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
								if($signAnesthesia2Id)
								{
								$table_pdf.='	
											<tr>
												<td>&nbsp;</td>
												<td colspan="2" style="text-align:left;">
													<b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signAnesthesia2DateTime).'
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
												<td class="bdrbtm" style="text-align:left;width:320px; ">Site Marked and Verified</td>
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
															<td style="width:200px;padding-left:10px; text-align:left;">';
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
							</table>
						</td>
					</tr>
					';
					}
					
	$table_pdf.='				
					<tr>
						<td style="width:150px;" class="bdrtop bdrbtm  pl5 bold">Patient Verified:&nbsp;';
						if($patientVerified!=''){
							$table_pdf.=$patientVerified."&nbsp;&nbsp;";
						}else{$table_pdf.="____";}
						$table_pdf.='
						</td>
						<td style="width:150px;" class="bdrtop bdrbtm ">Patient Height:&nbsp;';
						if($patientHeight!=''){
							$table_pdf.="<b>".$patientHeight."</b>";
						}else{$table_pdf.="____";}
						$table_pdf.='
						</td>
						<td style="width:150px;" class="bdrtop bdrbtm ">Patient Weight:&nbsp;';
						if($patientWeight!=''){
							$table_pdf.="<b>".$patientWeight."</b>";
						}else{$table_pdf.="____";}
						$table_pdf.='
						</td>
						<td style="width:150px;" class="bdrtop bdrbtm ">BMI:&nbsp;';
						if($BMIvalue!=''){
							$table_pdf.="<b>".$BMIvalue."</b>";
						}else{$table_pdf.="____";}
						$table_pdf.='
						</td>
					</tr>
					<tr>
						<td colspan="4" style="width:700px;" class="pl5 bdrbtm"><b>Procedure Verified&nbsp;&nbsp;</b>';
						if(trim($patient_primary_procedure)!=''){
							$table_pdf.='Primary Proc.:&nbsp;<b>'.$patient_primary_procedure."</b>&nbsp;&nbsp;&nbsp;";
						}
						if($patient_secondary_procedure!='' && $patient_secondary_procedure!='N/A'){
								$table_pdf.='Secondary Proc:&nbsp;<b>'.$patient_secondary_procedure."</b>";
						}
						$table_pdf.='
						</td>
					</tr>
					<tr>
						<td colspan="2" style="width:350px;" class="bdrbtm bgcolor bold pl5">Allergies</td>
						<td colspan="2" style="width:350px;" class="bdrbtm bgcolor bold pl5">Medication</td>
					</tr>';
					$table_pdf.='
					<tr>
						<td colspan="2" style="width:350px;vertical-align:top;">
							<table style="width:350px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Name</td>
									<td style="width:145px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Reaction</td>
								</tr>';
								if(count($getAllergiesName)>0){
									foreach($getAllergiesName as $keyGenAnes => $allergiesNameGenAnes){
										$table_pdf.='
										<tr>
											<td style="width:200px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($allergiesNameGenAnes)).'</td>
											<td style="width:145px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($getAllergiesRect[$keyGenAnes])).'</td>
										</tr>';
									}				
								}else{
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
						</td>';
						$medsQry=("select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_anesthesia_medication_tbl where confirmation_id=$pConfId ORDER BY prescription_medication_name");
						$medsRes=imw_query($medsQry);
						$medsnum=@imw_num_rows($medsRes);
						$table_pdf.='
						<td colspan="2" style="width:350px;">
							<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:125px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Name</td>
											<td style="width:100px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Dosage</td>
											<td style="width:100px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Sig</td>
										</tr>';
								if($medsnum>0){
									while($detailsMeds=imw_fetch_array($medsRes)){	
									$table_pdf.='
										<tr>
											<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_name']).'</td>
											<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_desc']).'</td>
											<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_sig']).'</td>
										</tr>';														
									}
								}else {
									for($q=1;$q<=3;$q++) {
										$table_pdf.='
										<tr>
											<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
											<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
										</tr>';
									}
								}
						$table_pdf.='		
							</table>
						</td>
					</tr>
					<tr>
						<td class="pl5 bdrbtm" colspan="4" style="width:700px;"><b>Patient Reassessed:&nbsp;</b>';
							if($PatientReassessed){
								$table_pdf.=$PatientReassessed;
							}else{
								$table_pdf.="____";
							}
						$table_pdf.='
						&nbsp;&nbsp;&nbsp;<b>Machine &amp; Equipment Completed:&nbsp;</b>';
							if($MachineEquipment){
								$table_pdf.=$MachineEquipment;
							}else{
								$table_pdf.="____";
							}
						$table_pdf.='
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Anesthesia Class:&nbsp;</b>';
						if($anesthesiaClass!=''){
							$valueClass="____";;
							if($anesthesiaClass=="one"){
								$valueClass="I";
							}
							elseif($anesthesiaClass=="two"){
								$valueClass="II";
							}
							elseif($anesthesiaClass=="three"){
								$valueClass="III";
						   	}
							if($valueClass){
								$table_pdf.=$valueClass;
							}
						}
						
						$table_pdf.='
						</td>
					</tr>
					<tr>
						<td colspan="4" style="width:700px;">
							<table style="width:700px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:150px;vertical-align:top;">
										<table style="width:150px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:150px;" class="bdrbtm">
												<b>Start Time:&nbsp;</b>';
												if($startTime && $startTime!="00:00 AM" && $startTime!="00:00 PM"){
													$table_pdf.=$startTime;
												}else{$table_pdf.="_____";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:150px;" class="bdrbtm">
												<b>Stop Time:&nbsp;</b>';
												if($stopTime && $stopTime!="00:00 AM" && $stopTime!="00:00 PM"){
													$table_pdf.=$stopTime;
												}else{$table_pdf.="_____";}
												$table_pdf.='
												</td>
											</tr>';
											if($macValue!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
													<b>MAC:&nbsp;</b>';
													if($macValue){
														$table_pdf.=ucwords($macValue);
													}else{$table_pdf.="_____";}
													$table_pdf.='
													</td>
												</tr>';
												
											}
											if($millarValue!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>Miller:&nbsp;</b>';
														if($millarValue){
															$table_pdf.=ucwords($millarValue);
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';
											}
											if($etTubeSize!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>ET Tube Size:&nbsp;</b>';
														if($etTubeSize){
															$table_pdf.=ucwords($etTubeSize);
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';
											}
											if($lmaSize!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>LMA Size:&nbsp;</b>';
														if($lmaSize){
															$table_pdf.=ucwords($lmaSize);
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';
											}
											if($mask!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>LMA Size:&nbsp;</b>';
														if($lmaSize){
															$table_pdf.=ucwords($lmaSize);
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';
											}
											if($mask!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>Mask:&nbsp;</b>';
														if($mask){
															$table_pdf.="Yes";
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';	
											}
											if($teethUnchanged!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>Teeth Unchanged:</b>';
														if($teethUnchanged){
															$table_pdf.=$teethUnchanged;
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';		
											}
											$table_pdf.='
												<tr>
													<td style="width:150px;" class="bgcolor bold">Monitor</td>
												</tr>';
											if($Monitor_ekg!=''){
												$table_pdf.='
												<tr>
													<td style="width:150px;" class="bdrbtm">
														<b>EKG:&nbsp;</b>';
														if($Monitor_ekg){
															$table_pdf.="Yes";
														}else{$table_pdf.="_____";}
														$table_pdf.='
													</td>
												</tr>';
											}
											$table_pdf.='
											<tr>
												<td style="width:150px;" class="bdrbtm">
													<b>ETCO<sub>2</sub>:&nbsp;</b>';
													if($Monitor_etco2){
														$table_pdf.="Yes";
													}else{$table_pdf.="_____";}
													$table_pdf.='
												</td>
											</tr>';
											$table_pdf.='
											<tr>
												<td style="width:150px;" class="bdrbtm">
													<b>O<sub>2</sub>Sat:&nbsp;</b>';
													if($Monitor_etco2Sat){
														$table_pdf.="Yes";
													}else{$table_pdf.="_____";}
													$table_pdf.='
												</td>
											</tr>';
											$table_pdf.='
											<tr>
												<td style="width:150px;" class="bdrbtm">
													<b>Temp:&nbsp;</b>';
													if($Monitor_o2Temp){
														$table_pdf.="Yes";
													}else{$table_pdf.="_____";}
													$table_pdf.='
												</td>
											</tr>';
											$table_pdf.='
											<tr>
												<td style="width:150px;height:20px;">
													<b>PNS:&nbsp;</b>';
													if($Monitor_PNS){
														$table_pdf.="Yes";
													}else{$table_pdf.="_____";}
													$table_pdf.='
												</td>
											</tr>
										</table>
									</td>
									<td style="width:400px;text-align:center;border:1px solid #C0C0C0;">';
									if($genAnesSignApplet!='' && file_exists('new_html2pdf/tess3.jpg')){
										$table_pdf.='<img src="../new_html2pdf/tess3.jpg" style="width:380px;height:300px;">';
									}else if(!empty($elem_cnvs_anes_drw_file)){										
										$updir = "admin";
										$img_src = $updir."/".$elem_cnvs_anes_drw_file;
										if(file_exists($img_src)){  $table_pdf.='<img src="../'.$img_src.'" style="width:380px;">';}
									}
									$table_pdf.='
									</td>
									<td style="width:150px;vertical-align:top;padding:0px;">
										<table style="width:170px;" cellpadding="0" cellspacing="0">
											<tr>
												<td style="width:165px;" class="bgcolor bold pl5">Arms</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Trucked:&nbsp;</b>';
												if($armsTuckedLeft!=""){
													$table_pdf.="<b>L</b>&nbsp;Yes&nbsp;&nbsp;";
												}
												if($armsTuckedRight!=""){
													$table_pdf.="<b>R</b>&nbsp;Yes";
												}
											$table_pdf.='</td>
											</tr>';
											$table_pdf.='
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Armboards:&nbsp;</b>';
												if($armsArmboardsLeft!=""){
													$table_pdf.="<b>L</b>&nbsp;Yes";
												}
												if($armsArmboardsRight!=""){
													$table_pdf.="&nbsp;<b>R</b>&nbsp;Yes";
												}
											$table_pdf.='</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bgcolor bold pl5">Eyes</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Taped:&nbsp;</b>';
												if($eyeTapedLeft!=""){
													$table_pdf.=$eyeTapedLeft;
												}else{$table_pdf.="______";}
											$table_pdf.='</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Lubed:&nbsp;</b>';
												if($eyeLubedLeft!=""){
													$table_pdf.=$eyeLubedLeft;
												}else{$table_pdf.="______";}
											$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:170px;font-size:12px;padding-left:2px;" class="bdrbtm"><b>Pressure Points Padded:&nbsp;</b>';
												if($pressurePointsPadded!=''){
													$table_pdf.="Yes";
												}else{$table_pdf.="_";}
											$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>BSS:&nbsp;</b>';
												if($bss!=''){
													$table_pdf.=$bss;
												}else{$table_pdf.="____";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Warming:&nbsp;</b>';
												if($warning!=''){
													$table_pdf.=$warning;
												}else{$table_pdf.="____";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:165px;" class="bdrbtm pl5"><b>Device temp:&nbsp;</b>';
												if($temp!=''){
													$table_pdf.=$temp;
												}else{$table_pdf.="____";}
												$table_pdf.='
												</td>
											</tr>
											<tr>
												<td style="width:165px;" class="pl5"><b>Graph Comments:</b>';
												if(trim($graphComments)){
													$table_pdf.="<br>".$graphComments;
												}else{$table_pdf.="____";}
												$table_pdf.='
												</td>
											</tr>';
											$table_pdf.='
										</table>
									</td>									
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="width:350px;" class="bdrbtm bdrtop"><b>Evaluation:&nbsp;</b>';
						if(trim($evaluation)){
							$table_pdf.=trim($evaluation);
						}else{$table_pdf.="_______";}
						$table_pdf.='
						</td>
						<td colspan="2" style="width:350px;" class="bdrbtm bdrtop"><b>Comments:&nbsp;</b>';
						if(trim($comments)){
							$table_pdf.=trim($comments);
						}else{$table_pdf.="_______";}
						$table_pdf.='
						</td>
					</tr>
					';
						// Vital Sign Grid Printing Section - Start
							
						if($vitalSignGridStatus)
						{
								$table_pdf.='<tr>';
								$table_pdf.='<td style="width:700px;" class="bdrbtm" colspan="4" valign="top"><table style="width:700px; " cellpadding="0" cellspacing="0">';
								
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
								$table_pdf.='<td rowspan="2" class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;<b>OSat<sub>2</sub></b></td>';		
								$table_pdf.='</tr>';
								$table_pdf.='<tr>';	
								
								$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Systolic</b></td>';
								$table_pdf.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Diastolic</b></td>';
								
								$table_pdf.='</tr>';
									
								
								
								$condArr		=	array();
								$condArr['confirmation_id']	=	$pConfId ;
								$condArr['chartName']				=	'genral_anesthesia_form' ;
								
								$gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time','Asc');
					
								$gCounter	=	0;
								if(is_array($gridData) && count($gridData) > 0  )
								{
									foreach($gridData as $gridRow)
									{		
										$gCounter++;
										//$fieldValue1= date('h:i A', strtotime($gridRow->start_time));
										$fieldValue1= $fieldValue1= $objManageData->getTmFormat($gridRow->start_time);
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
										$table_pdf.='<td class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;'.$fieldValue8.'</td>';		
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
										$table_pdf.='<td class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;</td>';		
										$table_pdf.='</tr>';
								}
								$table_pdf.='</table></td>';
								$table_pdf.='</tr>';
											
						}
										
						// Vital Sign Grid Printing Section - End
						
						$table_pdf.='
					<tr>
						<td colspan="2" style="width:350px;" class="bdrbtm"><b>PACU&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BP&nbsp;</b>';
							if($bp!=''){
								$table_pdf.=$bp."&nbsp;&nbsp;&nbsp;";
							}else{$table_pdf.="___&nbsp;&nbsp;";}
							$table_pdf.="<b>P</b>&nbsp;";
							if($P!=''){
								$table_pdf.=$P."&nbsp;&nbsp;&nbsp;";
							}else{$table_pdf.="___&nbsp;&nbsp;";}
							$table_pdf.="<b>RR</b>&nbsp;";
							if($P!=''){
								$table_pdf.=$rr."&nbsp;&nbsp;&nbsp;";
							}else{$table_pdf.="___&nbsp;&nbsp;";}
							$table_pdf.="<b>%SAO<sub>2</sub></b>&nbsp;";
							if($sao!=''){
								$table_pdf.=$sao."&nbsp;&nbsp;&nbsp;";
							}else{$table_pdf.="___&nbsp;&nbsp;";}
						$table_pdf.='
						</td>
						<td colspan="2" style="width:350px;" class="bdrbtm">';
						$table_pdf.='<b>Stable Cardio Respiratory:&nbsp;</b>';
							if($StableCardioRespiratory!=''){
								$table_pdf.=$StableCardioRespiratory;
							}else{$table_pdf.="___&nbsp;&nbsp;";}	
						$table_pdf.='	
						</td>
					</tr>
					<tr>
						<td colspan="2" style="width:350px;" class="bdrbtm pl5">';
							if($signAnesthesia1Status=="Yes"){
								$table_pdf.="<br><b>Anesthesia Provider:&nbsp;</b>".$signAnesthesia1Name;
								$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$table_pdf.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($ViewgenAnesRow["signAnesthesia1DateTime"]);
							}else {
								$table_pdf.="<br><b>Anesthesia Provider:&nbsp;</b>________";
								$table_pdf.="<br><b>Electronically Signed:&nbsp;</b>________";
								$table_pdf.="<br><b>Signature Date:&nbsp;</b>________";
							}
						$table_pdf.='
						</td>
						<td colspan="2" style="width:350px;" class="bdrbtm pl5">';
						if($relivedNurseId>0){
							$nurseName=$userNameArr[$relivedNurseId];	
						}	
						$table_pdf.="<b>Relief Nurse:&nbsp;</b>";
						if($relivedNurseId>0){
							$table_pdf.=$nurseName;
						}else{
							$table_pdf.="________";
						}
						$table_pdf.='
						</td>
					</tr>';
					
  				$table_pdf.='</table></page>';
				$table.=$table_pdf;
}
//=============================================================================//

//=============================General Nurse Note=============================//
//GET CURRENTLY LOGGED IN NURSE ID
	$currentLoggedinNurseQry = "select * from `patientconfirmation` where  patientConfirmationId = '".$_REQUEST["pConfId"]."'";
	$currentLoggedinNurseRes = imw_query($currentLoggedinNurseQry) or die(imw_error()); 
	$currentLoggedinNurseRow = imw_fetch_array($currentLoggedinNurseRes); 
	$currentLoggedinNurseId = $currentLoggedinNurseRow["nurseId"];
	
	//GET CURRENTLY LOGGED IN NURSE NAME
		$ViewNurseNameQry = "select * from `users` where  usersId = '".$currentLoggedinNurseId."'";
		$ViewNurseNameRes = imw_query($ViewNurseNameQry) or die(imw_error()); 
		$ViewNurseNameRow = imw_fetch_array($ViewNurseNameRes); 
		$currentLoggedinNurseName = $ViewNurseNameRow["lname"].", ".$ViewNurseNameRow["fname"]." ".$ViewNurseNameRow["mname"];
		$currentLoggedinNurseSignature = $ViewNurseNameRow["signature"];
	//END GET CURRENTLY LOGGED IN NURSE NAME

//END GET CURRENTLY LOGGED IN NURSE ID

//FUNCTION TO GET TIME FROM HIDDEN VALUE
	function get_timeValue($MainTime) {
		global $objManageData;
		if($MainTime == "00:00:00") {
			$MainTime = "";
		}else {
			$MainTime = $objManageData->getTmFormat($MainTime);//date('h:i A',strtotime($MainTime));
		}
		return $MainTime;
	}
//END FUNCTION TO GET TIME FROM HIDDEN VALUE

//FUNCTION TO SAVE TIME IN DATABASE
	function SaveTime($txt_NurseTime) {
	   if($txt_NurseTime<>"") {
		   $time_splitNotes = explode(" ",$txt_NurseTime);
		   
			if($time_splitNotes[1]=="PM" || $time_splitNotes[1]=="pm") {
				
				$time_splitNotestime = explode(":",$time_splitNotes[0]);
				$txt_NurseTimeIncr=$time_splitNotestime[0]+12;
				$txt_NurseTime = $txt_NurseTimeIncr.":".$time_splitNotestime[1].":00";
				
			}elseif($time_splitNotes[1]=="AM" || $time_splitNotes[1]=="am") {
				$time_splitNotestime = explode(":",$time_splitNotes[0]);
				$txt_NurseTime=$time_splitNotestime[0].":".$time_splitNotestime[1].":00";
				
				if($time_splitNotestime[0]=="00" && $time_splitNotestime[1]=="00") {
					$txt_NurseTime=$time_splitNotestime[0].":".$time_splitNotestime[1].":01";
				}
			}
			return $txt_NurseTime;
		}	
	}	

//FUNCTION TO SAVE TIME IN DATABASE





	//SET FORM STATUS ACCORDING TO MANDATORY FIELD
		$form_status = "completed";
		if($goalTPArchieve1=='' || $goalTPAchieve2=='' || $goalGEAchieve1==''
		 || $goalGEAchieve2=='' || $goalCRPAchieve1=='' || $goalCRPAchieve2==''
		 || $alert=='' || $hasTakenNourishment=='' || $nauseasVomiting==''
		 || $voidedQs=='' || $panv=='' || trim($dressing)==''
		 || ($monitorTissuePerfusion=='' && $implementComplicationMeasure==''
		 	 && $assessPlus=='' && $telemetry==''  && $otherTPNurseInter=='')
		 || ($assessRespiratoryStatus=='' && $positionOptimalChestExcusion==''
		 	 && $monitorOxygenationGE=='' && $otherGENurseInter=='')
		 || ($assessPain=='' && $usePharmacology==''
		 	 && $monitorOxygenationComfort=='' && $otherComfortNurseInter=='')
		 || $createdByUserId=='' || $comments=='' || $dischargeAt=='' 
		)
 
		{
			$form_status = "not completed";
		}
		
		
	//END SET FORM STATUS ACCORDING TO MANDATORY FIELD
	
	$chkNurseNotesQry = "select * from `genanesthesianursesnotes` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$chkNurseNotesRes = imw_query($chkNurseNotesQry) or die(imw_error()); 
	$chkNurseNotesNumRow = @imw_num_rows($chkNurseNotesRes);
	

//VIEW RECORD FROM DATABASE
		
		$genAnesNurseFormStatus='';
		$ViewgenAnesNurseQry = "select *, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat from `genanesthesianursesnotes` where  confirmation_id = '".$pConfId."'";
		$ViewgenAnesNurseRes = imw_query($ViewgenAnesNurseQry) or die(imw_error()); 
		$ViewgenAnesNurseNumRow = @imw_num_rows($ViewgenAnesNurseRes);
		$ViewgenAnesNurseRow = imw_fetch_array($ViewgenAnesNurseRes); 
		
		$genAnesNurseFormStatus = $ViewgenAnesNurseRow["form_status"];
		$anesthesiaGeneral = $ViewgenAnesNurseRow["anesthesiaGeneral"];
		$anesthesiaRegional = $ViewgenAnesNurseRow["anesthesiaRegional"];
		$anesthesiaEpidural = $ViewgenAnesNurseRow["anesthesiaEpidural"];
		$anesthesiaMAC = $ViewgenAnesNurseRow["anesthesiaMAC"];
		$anesthesiaLocal = $ViewgenAnesNurseRow["anesthesiaLocal"];
		$anesthesiaSpinal = $ViewgenAnesNurseRow["anesthesiaSpinal"];
		$anesthesiaSensationAt = $ViewgenAnesNurseRow["anesthesiaSensationAt"];
		$anesthesiaSensationAtDesc = $ViewgenAnesNurseRow["anesthesiaSensationAtDesc"];
		
		$genNotesSignApplet = $ViewgenAnesNurseRow["genNotesSignApplet"];
		$temp = $ViewgenAnesNurseRow["temp"];
		$o2Sat = $ViewgenAnesNurseRow["o2Sat"];
		$painScale = $ViewgenAnesNurseRow["painScale"];
		
		$intake_site1 = $ViewgenAnesNurseRow["intake_site1"];
		$intake_site2 = $ViewgenAnesNurseRow["intake_site2"];
		$intake_site3 = $ViewgenAnesNurseRow["intake_site3"];
		$intake_site4 = $ViewgenAnesNurseRow["intake_site4"];
		$intake_site5 = $ViewgenAnesNurseRow["intake_site5"];
		$intake_site6 = $ViewgenAnesNurseRow["intake_site6"];
		$intake_site7 = $ViewgenAnesNurseRow["intake_site7"];
		
		$intake_solution1 = $ViewgenAnesNurseRow["intake_solution1"];
		$intake_solution2 = $ViewgenAnesNurseRow["intake_solution2"];
		$intake_solution3 = $ViewgenAnesNurseRow["intake_solution3"];
		$intake_solution4 = $ViewgenAnesNurseRow["intake_solution4"];
		$intake_solution5 = $ViewgenAnesNurseRow["intake_solution5"];
		$intake_solution6 = $ViewgenAnesNurseRow["intake_solution6"];
		$intake_solution7 = $ViewgenAnesNurseRow["intake_solution7"];
		
		$intake_credit1 = $ViewgenAnesNurseRow["intake_credit1"];
		$intake_credit2 = $ViewgenAnesNurseRow["intake_credit2"];
		$intake_credit3 = $ViewgenAnesNurseRow["intake_credit3"];
		$intake_credit4 = $ViewgenAnesNurseRow["intake_credit4"];
		$intake_credit5 = $ViewgenAnesNurseRow["intake_credit5"];
		$intake_credit6 = $ViewgenAnesNurseRow["intake_credit6"];
		$intake_credit7 = $ViewgenAnesNurseRow["intake_credit7"];
			
		
		$alterTissuePerfusionTime1 = $ViewgenAnesNurseRow["alterTissuePerfusionTime1"];
		$hidd_alterTissuePerfusionTime1 = $ViewgenAnesNurseRow["alterTissuePerfusionTime1"];
		
		$alterTissuePerfusionTime2 = $ViewgenAnesNurseRow["alterTissuePerfusionTime2"];
		$hidd_alterTissuePerfusionTime2 = $ViewgenAnesNurseRow["alterTissuePerfusionTime2"];
		
		$alterTissuePerfusionTime3 = $ViewgenAnesNurseRow["alterTissuePerfusionTime3"];
		$hidd_alterTissuePerfusionTime3 = $ViewgenAnesNurseRow["alterTissuePerfusionTime3"];
		
		$alterTissuePerfusionInitials1 = $ViewgenAnesNurseRow["alterTissuePerfusionInitials1"];
		$alterTissuePerfusionInitials2 = $ViewgenAnesNurseRow["alterTissuePerfusionInitials2"];
		$alterTissuePerfusionInitials3 = $ViewgenAnesNurseRow["alterTissuePerfusionInitials3"];
		
		$alterGasExchangeTime1 = $ViewgenAnesNurseRow["alterGasExchangeTime1"];
		$hidd_alterGasExchangeTime1 = $ViewgenAnesNurseRow["alterGasExchangeTime1"];
		
		$alterGasExchangeTime2 = $ViewgenAnesNurseRow["alterGasExchangeTime2"];
		$hidd_alterGasExchangeTime2 = $ViewgenAnesNurseRow["alterGasExchangeTime2"];
		
		$alterGasExchangeTime3 = $ViewgenAnesNurseRow["alterGasExchangeTime3"];
		$hidd_alterGasExchangeTime3 = $ViewgenAnesNurseRow["alterGasExchangeTime3"];
		
		
		$alterGasExchangeInitials1 = $ViewgenAnesNurseRow["alterGasExchangeInitials1"];
		$alterGasExchangeInitials2 = $ViewgenAnesNurseRow["alterGasExchangeInitials2"];
		$alterGasExchangeInitials3 = $ViewgenAnesNurseRow["alterGasExchangeInitials3"];
	
		
		$alterComfortTime1 = $ViewgenAnesNurseRow["alterComfortTime1"];
		//$hidd_alterComfortTime1 = $ViewgenAnesNurseRow["alterComfortTime1"];
		
		$alterComfortTime2 = $ViewgenAnesNurseRow["alterComfortTime2"];
		//$hidd_alterComfortTime2 = $ViewgenAnesNurseRow["alterComfortTime2"];
		
		$alterComfortTime3 = $ViewgenAnesNurseRow["alterComfortTime3"];
		//$hidd_alterComfortTime3 = $ViewgenAnesNurseRow["alterComfortTime3"];
		
		$alterComfortInitials1 = $ViewgenAnesNurseRow["alterComfortInitials1"];
		$alterComfortInitials2 = $ViewgenAnesNurseRow["alterComfortInitials2"];
		$alterComfortInitials3 = $ViewgenAnesNurseRow["alterComfortInitials3"];
	
		
		
		$monitorTissuePerfusion = $ViewgenAnesNurseRow["monitorTissuePerfusion"];
		$implementComplicationMeasure = $ViewgenAnesNurseRow["implementComplicationMeasure"];
		$assessPlus = $ViewgenAnesNurseRow["assessPlus"];
		$telemetry = $ViewgenAnesNurseRow["telemetry"];
		$otherTPNurseInter = $ViewgenAnesNurseRow["otherTPNurseInter"];
		$otherTPNurseInterDesc = $ViewgenAnesNurseRow["otherTPNurseInterDesc"];
		$assessRespiratoryStatus = $ViewgenAnesNurseRow["assessRespiratoryStatus"];
		$positionOptimalChestExcusion = $ViewgenAnesNurseRow["positionOptimalChestExcusion"];
		$monitorOxygenationGE = $ViewgenAnesNurseRow["monitorOxygenationGE"];
		$otherGENurseInter = $ViewgenAnesNurseRow["otherGENurseInter"];
		$otherGENurseInterDesc = $ViewgenAnesNurseRow["otherGENurseInterDesc"];
		$assessPain = $ViewgenAnesNurseRow["assessPain"];
		$usePharmacology = $ViewgenAnesNurseRow["usePharmacology"];
		$monitorOxygenationComfort = $ViewgenAnesNurseRow["monitorOxygenationComfort"];
		$otherComfortNurseInter = $ViewgenAnesNurseRow["otherComfortNurseInter"];
		$otherComfortNurseInterDesc = $ViewgenAnesNurseRow["otherComfortNurseInterDesc"]; 
		
		$goalTPAchieveTime1 = $ViewgenAnesNurseRow["goalTPAchieveTime1"]; 
		$hidd_goalTPAchieveTime1 = $ViewgenAnesNurseRow["goalTPAchieveTime1"]; 
		$goalTPArchieve1 = $ViewgenAnesNurseRow["goalTPArchieve1"]; 
		$goalTPAchieveInitial1  = $ViewgenAnesNurseRow["goalTPAchieveInitial1"]; 
		
		$goalTPAchieveTime2 = $ViewgenAnesNurseRow["goalTPAchieveTime2"]; 
		$hidd_goalTPAchieveTime2 = $ViewgenAnesNurseRow["goalTPAchieveTime2"]; 
		$goalTPAchieve2 = $ViewgenAnesNurseRow["goalTPAchieve2"]; 
		$goalTPAchieveInitial2  = $ViewgenAnesNurseRow["goalTPAchieveInitial2"]; 
		
		$goalGEAchieveTime1 = $ViewgenAnesNurseRow["goalGEAchieveTime1"]; 
		$hidd_goalGEAchieveTime1 = $ViewgenAnesNurseRow["goalGEAchieveTime1"]; 
		$goalGEAchieve1 = $ViewgenAnesNurseRow["goalGEAchieve1"]; 
		$goalGEAchieveInitial1  = $ViewgenAnesNurseRow["goalGEAchieveInitial1"]; 
	
		$goalGEAchieveTime2 = $ViewgenAnesNurseRow["goalGEAchieveTime2"]; 
		$hidd_goalGEAchieveTime2 = $ViewgenAnesNurseRow["goalGEAchieveTime2"]; 
		$goalGEAchieve2 = $ViewgenAnesNurseRow["goalGEAchieve2"]; 
		$goalGEAchieveInitial2  = $ViewgenAnesNurseRow["goalGEAchieveInitial2"]; 
		
		$goalCRPAchieveTime1 = $ViewgenAnesNurseRow["goalCRPAchieveTime1"]; 
		$hidd_goalCRPAchieveTime1 = $ViewgenAnesNurseRow["goalCRPAchieveTime1"]; 
		$goalCRPAchieve1 = $ViewgenAnesNurseRow["goalCRPAchieve1"]; 
		$goalCRPAchieveInitial1  = $ViewgenAnesNurseRow["goalCRPAchieveInitial1"]; 
		
		$goalCRPAchieveTime2 = $ViewgenAnesNurseRow["goalCRPAchieveTime2"]; 
		$hidd_goalCRPAchieveTime2 = $ViewgenAnesNurseRow["goalCRPAchieveTime2"]; 
		$goalCRPAchieve2 = $ViewgenAnesNurseRow["goalCRPAchieve2"]; 
		$goalCRPAchieveInitial2  = $ViewgenAnesNurseRow["goalCRPAchieveInitial2"]; 
		
		$dischargeSummaryTemp = $ViewgenAnesNurseRow["dischargeSummaryTemp"];
		$dischangeSummaryBp = $ViewgenAnesNurseRow["dischangeSummaryBp"];
		$dischargeSummaryP = $ViewgenAnesNurseRow["dischargeSummaryP"];
		$dischangeSummaryRR = $ViewgenAnesNurseRow["dischangeSummaryRR"];
		
		$alert = $ViewgenAnesNurseRow["alert"];
		$hasTakenNourishment = $ViewgenAnesNurseRow["hasTakenNourishment"];
		$nauseasVomiting = $ViewgenAnesNurseRow["nauseasVomiting"];
		$voidedQs = $ViewgenAnesNurseRow["voidedQs"];
		$panv = $ViewgenAnesNurseRow["panv"];
		$dressing = $ViewgenAnesNurseRow["dressing"];
		$dischargeSummaryOther = $ViewgenAnesNurseRow["dischargeSummaryOther"];
		$dischargeAt = $ViewgenAnesNurseRow["dischargeAt"];
		$comments = $ViewgenAnesNurseRow["comments"];
		$form_status = $ViewgenAnesNurseRow["form_status"];
			list($list_hour,$list_min,$secnd) = explode(":",$dischargeAt);
			//echo $list_hour.$list_min;
			if($list_hour>12) {
				$list_hour=$list_hour-12;
				$list_ampm="PM";
			}
		$dischargeAtTime=	$list_hour.':'.$list_min.''.$list_ampm;
		$nurseId = $ViewgenAnesNurseRow["nurseId"];
		$nurseSign = $ViewgenAnesNurseRow["nurseSign"];
		
		$whoUserType = $ViewgenAnesNurseRow["whoUserType"];
		$whoUserTypeLabel = ($whoUserType == 'Anesthesiologist') ? 'Anesthesia&nbsp;Provider' : $whoUserType ;
		$createdByUserId = $ViewgenAnesNurseRow["createdByUserId"];
		$relivedNurseId = $ViewgenAnesNurseRow["relivedNurseId"];
		
		
		$signNurseId =  $ViewgenAnesNurseRow["signNurseId"];
		$signNurseFirstName =  $ViewgenAnesNurseRow["signNurseFirstName"];
		$signNurseMiddleName =  $ViewgenAnesNurseRow["signNurseMiddleName"];
		$signNurseLastName =  $ViewgenAnesNurseRow["signNurseLastName"]; 
		$signNurseStatus =  $ViewgenAnesNurseRow["signNurseStatus"];
		
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
		//$signNurseDateTime =  $ViewPreopnursingRow["signNurseDateTime"];
		
		$recovery_new_drugs = array();
		$recovery_new_dose = array();
		$recovery_new_route = array();
		$recovery_new_time = array();
		$recovery_new_initial = array();
		for($i=1;$i<=4;$i++) {
			$recovery_new_drugs[$i] =  $ViewgenAnesNurseRow["recovery_new_drugs$i"];
			$recovery_new_dose[$i] =  $ViewgenAnesNurseRow["recovery_new_dose$i"];
			$recovery_new_route[$i] =  $ViewgenAnesNurseRow["recovery_new_route$i"];
			$recovery_new_time[$i] =  get_timeValue($ViewgenAnesNurseRow["recovery_new_time$i"]);
			$recovery_new_initial[$i] =  $ViewgenAnesNurseRow["recovery_new_initial$i"];
			
		}
		
		$intake_new_fluids = array();
		$intake_new_amount_given = array();
		for($j=1;$j<=3;$j++) {
			$intake_new_fluids[$j] =  $ViewgenAnesNurseRow["intake_new_fluids$j"];
			$intake_new_amount_given[$j] =  $ViewgenAnesNurseRow["intake_new_amount_given$j"];
			
		}
		
		//
		$recovery_new_drugs = array();
		$recovery_new_dose = array();
		$recovery_new_route = array();
		$recovery_new_time = array();
		$recovery_new_initial = array();
		for($i=1;$i<=4;$i++) {
			$recovery_new_drugs[$i] =  $ViewgenAnesNurseRow["recovery_new_drugs$i"];
			$recovery_new_dose[$i] =  $ViewgenAnesNurseRow["recovery_new_dose$i"];
			$recovery_new_route[$i] =  $ViewgenAnesNurseRow["recovery_new_route$i"];
			$recovery_new_time[$i] =  get_timeValue($ViewgenAnesNurseRow["recovery_new_time$i"]);
			$recovery_new_initial[$i] =  $ViewgenAnesNurseRow["recovery_new_initial$i"];
			
		}
		//
		
		$ascId = $ViewgenAnesNurseRow["ascId"];
		$confirmation_id = $ViewgenAnesNurseRow["confirmation_id"];
		$patient_id = $ViewgenAnesNurseRow["patient_id"];
		
		
			
		//CODE TO SET gennurseNotes TISSUE TIME
			if($alterTissuePerfusionTime1=="00:00:00" || $alterTissuePerfusionTime1=="") {
				$hidd_alterTissuePerfusionTime1 = "";//date("H:i:s");
			}else {
				$hidd_alterTissuePerfusionTime1 = 	$alterTissuePerfusionTime1;
			}	
			
			if($alterTissuePerfusionTime2=="00:00:00" || $alterTissuePerfusionTime2=="") {
				$hidd_alterTissuePerfusionTime2 = "";//date("H:i:s");
			}else {
				$hidd_alterTissuePerfusionTime2 = 	$alterTissuePerfusionTime2;
			}
			
			if($alterTissuePerfusionTime3=="00:00:00" || $alterTissuePerfusionTime3=="") {
				$hidd_alterTissuePerfusionTime3 = "";//date("H:i:s");
			}else {
				$hidd_alterTissuePerfusionTime3 = 	$alterTissuePerfusionTime3;
			}
			
			$alterTissuePerfusionTime1 = get_timeValue($hidd_alterTissuePerfusionTime1);
			$alterTissuePerfusionTime2 = get_timeValue($hidd_alterTissuePerfusionTime2);
			$alterTissuePerfusionTime3 = get_timeValue($hidd_alterTissuePerfusionTime3);
		//END CODE TO SET gennurseNotes TISSUE TIME
		
		//CODE TO SET gennurseNotes GAS EXCHANGE TIME
			if($alterGasExchangeTime1=="00:00:00" || $alterGasExchangeTime1=="") {
				$hidd_alterGasExchangeTime1 = "";//date("H:i:s");
			}else {
				$hidd_alterGasExchangeTime1 = 	$alterGasExchangeTime1;
			}	
			
			if($alterGasExchangeTime2=="00:00:00" || $alterGasExchangeTime2=="") {
				$hidd_alterGasExchangeTime2 = "";//date("H:i:s");
			}else {
				$hidd_alterGasExchangeTime2 = 	$alterGasExchangeTime2;
			}
			
			if($alterGasExchangeTime3=="00:00:00" || $alterGasExchangeTime3=="") {
				$hidd_alterGasExchangeTime3 = "";//date("H:i:s");
			}else {
				$hidd_alterGasExchangeTime3 = 	$alterGasExchangeTime3;
			}
			
			$alterGasExchangeTime1 = get_timeValue($hidd_alterGasExchangeTime1);
			$alterGasExchangeTime2 = get_timeValue($hidd_alterGasExchangeTime2);
			$alterGasExchangeTime3 = get_timeValue($hidd_alterGasExchangeTime3);
		//END CODE TO SET gennurseNotes GAS EXCHANGE TIME
		
		//CODE TO SET gennurseNotes CONFORT TIME
			if($alterComfortTime1=="00:00:00" || $alterComfortTime1=="") {
				$hidd_alterComfortTime1 = "";//date("H:i:s");
			}else {
				$hidd_alterComfortTime1 = 	$alterComfortTime1;
			}	
			
			if($alterComfortTime2=="00:00:00" || $alterComfortTime2=="") {
				$hidd_alterComfortTime2 = "";//date("H:i:s");
			}else {
				$hidd_alterComfortTime2 = 	$alterComfortTime2;
			}
			
			if($alterComfortTime3=="00:00:00" || $alterComfortTime3=="") {
				$hidd_alterComfortTime3 = "";//date("H:i:s");
			}else {
				$hidd_alterComfortTime3 = 	$alterComfortTime3;
			}
			
			$alterComfortTime1 = get_timeValue($hidd_alterComfortTime1);
			$alterComfortTime2 = get_timeValue($hidd_alterComfortTime2);
			$alterComfortTime3 = get_timeValue($hidd_alterComfortTime3);
		//END CODE TO SET gennurseNotes CONFORT TIME
		
		//CODE TO SET gennurseNotes GOAL TP ACHIVE TIME
			if($goalTPAchieveTime1=="00:00:00" || $goalTPAchieveTime1=="") {
				$hidd_goalTPAchieveTime1 = "";//date("H:i:s");
			}else {
				$hidd_goalTPAchieveTime1 = 	$goalTPAchieveTime1;
			}	
			
			if($goalTPAchieveTime2=="00:00:00" || $goalTPAchieveTime2=="") {
				$hidd_goalTPAchieveTime2 = "";//date("H:i:s");
			}else {
				$hidd_goalTPAchieveTime2 = 	$goalTPAchieveTime2;
			}
			$goalTPAchieveTime1 = get_timeValue($hidd_goalTPAchieveTime1);
			$goalTPAchieveTime2 = get_timeValue($hidd_goalTPAchieveTime2);
		//END CODE TO SET gennurseNotes GOAL TP ACHIVE TIME
		
		//CODE TO SET gennurseNotes GOAL GE ACHIVE TIME
			if($goalGEAchieveTime1=="00:00:00" || $goalGEAchieveTime1=="") {
				$hidd_goalGEAchieveTime1 = "";//date("H:i:s");
			}else {
				$hidd_goalGEAchieveTime1 = 	$goalGEAchieveTime1;
			}	
			
			if($goalGEAchieveTime2=="00:00:00" || $goalGEAchieveTime2=="") {
				$hidd_goalGEAchieveTime2 = "";//date("H:i:s");
			}else {
				$hidd_goalGEAchieveTime2 = 	$goalGEAchieveTime2;
			}
			$goalGEAchieveTime1 = get_timeValue($hidd_goalGEAchieveTime1);
			$goalGEAchieveTime2 = get_timeValue($hidd_goalGEAchieveTime2);
		//END TO SET gennurseNotes GOAL GE ACHIVE TIME
		
		//CODE TO SET gennurseNotes GOAL CRP ACHIVE TIME
			if($goalCRPAchieveTime1=="00:00:00" || $goalCRPAchieveTime1=="") {
				$hidd_goalCRPAchieveTime1 = "";//date("H:i:s");
			}else {
				$hidd_goalCRPAchieveTime1 = 	$goalCRPAchieveTime1;
			}	
			
			if($goalCRPAchieveTime2=="00:00:00" || $goalCRPAchieveTime2=="") {
				$hidd_goalCRPAchieveTime2 = "";//date("H:i:s");
			}else {
				$hidd_goalCRPAchieveTime2 = 	$goalCRPAchieveTime2;
			}
			$goalCRPAchieveTime1 = get_timeValue($hidd_goalCRPAchieveTime1);
			$goalCRPAchieveTime2 = get_timeValue($hidd_goalCRPAchieveTime2);
		//END CODE TO SET gennurseNotes GOAL CRP ACHIVE TIME
	
//END VIEW RECORD FROM DATABASE
if($genAnesNurseFormStatus=='completed' || $genAnesNurseFormStatus=='not completed'){
	$newnotesQry 	= "select * from `genanesthesianursesnewnotes` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$newnotesRes 	= imw_query($newnotesQry) or die(imw_error()); 
	$newnotesNumRow = imw_num_rows($newnotesRes);
	$table_pdf_new ='';
	$table_pdf_new.='<page backbottom="5mm">'.$head_table;
	$table_pdf_new.="<style>.bdrbtm{vertical-align:middle;}</style>";
	$table_pdf_new.='
		<table style="width:700px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
         	<tr>
				<td colspan="2" style="width:700px;" class="fheader">General Anesthesia Nurses Notes</td>
			</tr>
			<tr>
				<td style="width:350px;" class="bdrbtm bgcolor bold pl5">Allergies</td>
				<td style="width:350px;" class="bdrbtm bgcolor bold pl5">Anesthesia</td>
			</tr>';
					
			$table_pdf_new.='
			<tr>
				<td style="width:350px;vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Name</td>
							<td style="width:145px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Reaction</td>
						</tr>';
						if(count($getAllergiesName)>0){
							foreach($getAllergiesName as $keyGenNrs=> $allergiesNameGenNrs){
								$table_pdf_new.='
								<tr>
									<td style="width:200px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($allergiesNameGenNrs)).'</td>
									<td style="width:145px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($getAllergiesRect[$keyGenNrs])).'</td>
								</tr>';
							}				
						}else{
						$table_pdf_new.='
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
					$table_pdf_new.='	
					</table>
				</td>';
				$table_pdf_new.='
				<td style="width:350px;vertical-align:top;">
					<table style="width:350px;border-left:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="4" style="width:336px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">&nbsp;</td>
						</tr>';
						$table_pdf_new.='
							<tr>
								<td style="width:84px;" class="bdrbtm pl5">General&nbsp;';	if($anesthesiaGeneral)  	{$table_pdf_new	.= '<b>'.$anesthesiaGeneral.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
								<td style="width:90px;" class="bdrbtm pl5">Regional&nbsp;';	if($anesthesiaRegional) 	{$table_pdf_new	.= '<b>'.$anesthesiaRegional.'</b>';}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
								<td style="width:84px;" class="bdrbtm pl5">Epidural&nbsp;';	if($anesthesiaEpidural) 	{$table_pdf_new	.= '<b>'.$anesthesiaEpidural.'</b>';}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
								<td style="width:78px;" class="bdrbtm pl5">MAC&nbsp;';		if($anesthesiaMAC)			{$table_pdf_new	.= '<b>'.$anesthesiaMAC.'</b>';		}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
							</tr>
							<tr>
								<td style="width:84px;" class="bdrbtm pl5">Local&nbsp;';	if($anesthesiaLocal)  		{$table_pdf_new	.= '<b>'.$anesthesiaLocal.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
								<td style="width:90px;" class="bdrbtm pl5">Spinal&nbsp;';	if($anesthesiaSpinal) 		{$table_pdf_new	.= '<b>'.$anesthesiaSpinal.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
								<td colspan="2" style="width:162px;" class="bdrbtm pl5">Sensation At&nbsp;';if($anesthesiaSensationAtDesc) {$table_pdf_new	.= '<b>'.$anesthesiaSensationAtDesc.'</b>';}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
							</tr>
							';
				$table_pdf_new.='
					</table>
				</td>
			</tr>';
			if($genNotesSignApplet!='') {
				$table_pdf_new.='
				<tr>
					<td colspan="2"  valign="top" class="bdrbtm" ><b>Applet Data</b><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">&nbsp;&nbsp;&nbsp;<img src="../new_html2pdf/tess_nurse_notes.jpg" width="350" height="300"></font></td>
				</tr>
				';
			}
			
			
			$table_pdf_new.='
			<tr>
				<td colspan="2"  valign="top" >
					<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>
							<td rowspan="5" style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Recovery Room Meds';if(trim($ViewgenAnesNurseRow["recovery_room_na"])=="Yes") {$table_pdf_new.=' - N/A';}$table_pdf_new.='</td>
							<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Medication</td>
							<td style="width:140px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Dose</td>
							<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Route</td>
							<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Time</td>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Initial</td>
						</tr>';
					for($i=1;$i<=4;$i++) {
						$table_pdf_new.='
						<tr>
							<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($ViewgenAnesNurseRow["recovery_new_drugs".$i]).'</td>
							<td style="width:140px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($ViewgenAnesNurseRow["recovery_new_dose".$i]).'</td>
							<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if(trim($ViewgenAnesNurseRow["recovery_new_drugs".$i])) {$table_pdf_new.=$ViewgenAnesNurseRow["recovery_new_route".$i];}$table_pdf_new.='</td>
							<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["recovery_new_time".$i]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["recovery_new_time".$i]);}$table_pdf_new.='</td>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["recovery_new_initial".$i]]).'</td>
						</tr>';
					}
						$table_pdf_new.='
						<tr>
							<td rowspan="4" style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Intake</td>
							<td colspan="2" style="width:320px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">IV fluids</td>
							<td colspan="2" style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Amount Given</td>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">&nbsp;</td>
						</tr>';
					
					for($j=1;$j<=3;$j++) {
						$table_pdf_new.='
						<tr>
							<td colspan="2" style="width:320px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($ViewgenAnesNurseRow["intake_new_fluids".$j]).'</td>
							<td colspan="2" style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($ViewgenAnesNurseRow["intake_new_amount_given".$j]).'</td>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm">&nbsp;</td>
						</tr>';
					}
					
					if($newnotesNumRow>0) {
						$table_pdf_new.='
						<tr>
							<td style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Time</td>
							<td colspan="5" style="width:620px;border-right:1px solid #C0C0C0;" class="bdrbtm  bold">Nurses Notes</td>
						</tr>';
					  while($newnotesRow = imw_fetch_array($newnotesRes)) {
						$table_pdf_new.='
						<tr>
							<td style="width:100px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($newnotesRow["newnotes_time"]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($newnotesRow["newnotes_time"]);}$table_pdf_new.='</td>
							<td colspan="5" style="width:620px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($newnotesRow["newnotes_desc"]).'</td>
						</tr>';
						  
					  }
						
					}
						$table_pdf_new.='
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" >
					<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bgcolor bold">NURSING DIAGNOSIS</td>
							<td style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bgcolor bold">NURSING INTERVENTION</td>
							<td style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bgcolor bold">GOAL/EVALUATION</td>
						</tr>
						<tr>
							<td style="width:240px;border-right:1px solid #C0C0C0;vertical-align:top;" class="bdrbtm">
								<table style="width:240px;" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="2" style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Alteration in tissue perfusion</td>
									</tr>
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($k=1;$k<=3;$k++) {
									$table_pdf_new.='
									
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["alterTissuePerfusionTime".$k]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["alterTissuePerfusionTime".$k]);}$table_pdf_new.='</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["alterTissuePerfusionInitials".$k]]).'</td>
									</tr>';
								}
									$table_pdf_new.='
									<tr>
										<td colspan="2" style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Alteration in gas exchange</td>
									</tr>
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($l=1;$l<=3;$l++) {
									$table_pdf_new.='
									
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["alterGasExchangeTime".$l]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["alterGasExchangeTime".$l]);}$table_pdf_new.='</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["alterGasExchangeInitials".$l]]).'</td>
									</tr>';
								}
									$table_pdf_new.='
									<tr>
										<td colspan="2" style="width:240px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Alteration in comfort related pain</td>
									</tr>
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($m=1;$m<=3;$m++) {
									$table_pdf_new.='
									
									<tr>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["alterComfortTime".$m]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["alterComfortTime".$m]);}$table_pdf_new.='</td>
										<td style="width:180px;border-right:1px solid #C0C0C0;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["alterComfortInitials".$m]]).'</td>
									</tr>';
								}
									$table_pdf_new.='

								</table>
							</td>
							<td style="width:240px;border-right:1px solid #C0C0C0;vertical-align:top;" class="bdrbtm">
								<table style="width:240px;" cellpadding="0" cellspacing="0">
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Monitor the patient for changes in systemic and peripheral tissue perfusion</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($monitorTissuePerfusion) {$table_pdf_new	.= '<b>'.$monitorTissuePerfusion.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Implement measure to minimize complication and on dimished perfusion</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($implementComplicationMeasure) {$table_pdf_new	.= '<b>'.$implementComplicationMeasure.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Assess Pulse</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($assessPlus) {$table_pdf_new	.= '<b>'.$assessPlus.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Telemetry</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($telemetry) {$table_pdf_new	.= '<b>'.$telemetry.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Other&nbsp;';if(trim($otherTPNurseInterDesc)) {$table_pdf_new	.= '<b>'.stripslashes($otherTPNurseInterDesc).'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($otherTPNurseInter) {$table_pdf_new	.= '<b>'.$otherTPNurseInter.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Assess respiratory status</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($assessRespiratoryStatus) {$table_pdf_new	.= '<b>'.$assessRespiratoryStatus.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Position the patient for optimal chest excursion and its exchange</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($positionOptimalChestExcusion) {$table_pdf_new	.= '<b>'.$positionOptimalChestExcusion.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Monitor oxygenation</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($monitorOxygenationGE) {$table_pdf_new	.= '<b>'.$monitorOxygenationGE.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Other&nbsp;';if(trim($otherGENurseInterDesc)) {$table_pdf_new	.= '<b>'.stripslashes($otherGENurseInterDesc).'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($otherGENurseInter) {$table_pdf_new	.= '<b>'.$otherGENurseInter.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Assess pain</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($assessPain) {$table_pdf_new	.= '<b>'.$assessPain.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Use pharmacology interventions to relieve pain</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($usePharmacology) {$table_pdf_new	.= '<b>'.$usePharmacology.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Monitor oxygenation</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($monitorOxygenationComfort) {$table_pdf_new	.= '<b>'.$monitorOxygenationComfort.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
									<tr>
										<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm ">Other&nbsp;';if(trim($otherComfortNurseInterDesc)) {$table_pdf_new	.= '<b>'.stripslashes($otherComfortNurseInterDesc).'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">';if($otherComfortNurseInter) {$table_pdf_new	.= '<b>'.$otherComfortNurseInter.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
									</tr>
								</table>
							</td>
							<td style="width:240px;border-right:1px solid #C0C0C0;vertical-align:top;" class="bdrbtm">
								<table style="width:230px;" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="3" style="width:220px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Patient will show signs of adequate tissue perfusion</td>
									</tr>
									<tr>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Achieve</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:120px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($p=1;$p<=2;$p++) {
									$goalTPAchieve = $ViewgenAnesNurseRow["goalTPArchieve".$p];
									if($p==2) {$goalTPAchieve = $ViewgenAnesNurseRow["goalTPAchieve".$p];}
									$table_pdf_new.='
									
									<tr>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($goalTPAchieve) {$table_pdf_new	.= '<b>'.$goalTPAchieve.'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["goalTPAchieveTime".$p]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["goalTPAchieveTime".$p]);}$table_pdf_new.='</td>
										<td style="width:120px;border-right:1px solid #C0C0C0;font-size:12px;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["goalTPAchieveInitial".$p]]).'</td>
									</tr>';
								}
									$table_pdf_new.='
									<tr>
										<td colspan="3" style="width:220px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Maintain patient airways with adequate exchange</td>
									</tr>
									<tr>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Achieve</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:120px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($q=1;$q<=2;$q++) {
									$table_pdf_new.='
									<tr>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["goalGEAchieve".$q]) {$table_pdf_new.= '<b>'.$ViewgenAnesNurseRow["goalGEAchieve".$q].'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["goalGEAchieveTime".$q]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["goalGEAchieveTime".$q]);}$table_pdf_new.='</td>
										<td style="width:120px;border-right:1px solid #C0C0C0;font-size:12px;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["goalGEAchieveInitial".$q]]).'</td>
									</tr>';
								}
									$table_pdf_new.='
									<tr>
										<td colspan="3" style="width:220px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Patient denies or shows no sign of excessive pain</td>
									</tr>
									<tr>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Achieve</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Time</td>
										<td style="width:120px;border-right:1px solid #C0C0C0;" class="bdrbtm bold">Initials</td>
									</tr>';
								for($r=1;$r<=2;$r++) {
									$table_pdf_new.='
									<tr>
										<td style="width:40px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["goalCRPAchieve".$r]) {$table_pdf_new.= '<b>'.$ViewgenAnesNurseRow["goalCRPAchieve".$r].'</b>';	}else{$table_pdf_new.="___";}$table_pdf_new.='</td>
										<td style="width:60px;border-right:1px solid #C0C0C0;" class="bdrbtm">';if($ViewgenAnesNurseRow["goalCRPAchieveTime".$r]!="00:00:00"){$table_pdf_new.=$objManageData->getTmFormat($ViewgenAnesNurseRow["goalCRPAchieveTime".$r]);}$table_pdf_new.='</td>
										<td style="width:120px;border-right:1px solid #C0C0C0;font-size:12px;" class="bdrbtm">'.stripslashes($userNameArr[$ViewgenAnesNurseRow["goalCRPAchieveInitial".$r]]).'</td>
									</tr>';
								}
									$table_pdf_new.='
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="width:350px; border:1px solid #C0C0C0; vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="4" style="width:355px; border:1px solid #C0C0C0;" class="bold bdrbtm pl5 bgcolor">DISCHARGE SUMMARY</td>
						</tr>
						<tr>
							<td style="width:85px;" class="bdrbtm "><b>Temp</b>&nbsp;';
							if($dischargeSummaryTemp){
								$table_pdf_new.=$dischargeSummaryTemp;	
							}else{
								$table_pdf_new.="___";
							}
							$table_pdf_new.='
							</td>
							<td style="width:85px;" class="bdrbtm"><b>BP</b>&nbsp;';
							if($dischangeSummaryBp){
								$table_pdf_new.=$dischangeSummaryBp;	
							}else{
								$table_pdf_new.="___";
							}
							$table_pdf_new.='
							</td>
							<td style="width:85px;" class="bdrbtm"><b>P</b>&nbsp;';
							if($dischargeSummaryP){
								$table_pdf_new.=$dischargeSummaryP;	
							}else{
								$table_pdf_new.="___";
							}
							$table_pdf_new.='
							</td>
							<td style="width:85px;" class="bdrbtm"><b>BP</b>&nbsp;';
							if($dischangeSummaryRR){
								$table_pdf_new.=$dischangeSummaryRR;	
							}else{
								$table_pdf_new.="___";
							}
							$table_pdf_new.='
							</td>
						</tr>
						<tr>
							<td style="width:85px;" class="bdrbtm">Dressing</td>
							<td colspan="3" style="width:250px;" class="bdrbtm">';
							if($dressing){
								$table_pdf_new.=$dressing;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>
						<tr>
							<td style="width:85px;" class="bdrbtm">Other</td>
							<td colspan="3" style="width:250px;" class="bdrbtm">';
							if($dischargeSummaryOther){
								$table_pdf_new.=$dischargeSummaryOther;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>
						<tr>
							<td style="width:85px;" class="bdrbtm">Discharge At</td>
							<td colspan="3" style="width:250px;" class="bdrbtm">';
							if($dischargeAtTime){
								$table_pdf_new.=$objManageData->getTmFormat($dischargeAtTime);
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>
						
					</table>
				</td>
				<td style="width:350px; border:1px solid #C0C0C0;vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:250px;" class="bdrbtm bgcolor pl5"></td>
							<td style="width:100px;" class="bdrbtm cbold bgcolor pl5">Yes/No</td>
						</tr>
						<tr>
							<td style="width:250px;" class="bdrbtm pl5">Alert</td>
							<td style="width:100px;" class="bdrbtm cbold pl5">';
							if($alert){
								$table_pdf_new.=$alert;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>	
						<tr>
							<td style="width:250px;" class="bdrbtm pl5">Has taken nourishment</td>
							<td style="width:100px;" class="bdrbtm cbold  pl5">';
							if($hasTakenNourishment){
								$table_pdf_new.=$hasTakenNourishment;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>	
						<tr>
							<td style="width:250px;" class="bdrbtm pl5">Nausea Vomiting</td>
							<td style="width:100px;" class="bdrbtm pl5 cbold">';
							if($nauseasVomiting){
								$table_pdf_new.=$nauseasVomiting;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>	
						<tr>
							<td style="width:250px;" class="bdrbtm pl5">Voided Q.S.</td>
							<td style="width:100px;" class="bdrbtm pl5 cbold">';
							if($voidedQs){
								$table_pdf_new.=$voidedQs;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>	
						<tr>
							<td style="width:250px;" class="bdrbtm pl5">Pain</td>
							<td style="width:100px;" class="bdrbtm pl5 cbold">';
							if($panv){
								$table_pdf_new.=$panv;
							}else{
								$table_pdf_new.="___";	
							}
							$table_pdf_new.='
							</td>
						</tr>	
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="width:700px;">
					<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:85px;" class="bdrbtm"><b>Comments:</b></td>
							<td style="width:200px;" class="bdrbtm">';
							if($comments){
								$table_pdf_new.=stripslashes($comments);
							}else{
								$table_pdf_new.="______";	
							}
							$table_pdf_new.='
							</td>
							<td style="width:200px;" class="bdrbtm"><b>User Type:</b>&nbsp;';
							if($whoUserType){
								$table_pdf_new.=$whoUserTypeLabel;
							}else{
								$table_pdf_new.="______";	
							}
							$table_pdf_new.='
							</td>
							<td style="width:250px;" class="bdrbtm"><b>Created By:&nbsp;</b>';
							if($createdByUserId && $userNameArr[$createdByUserId]){
								
								$table_pdf_new.=$userNameArr[$createdByUserId];
							}else{
								$table_pdf_new.="______";	
							}
							$table_pdf_new.='
							</td>
						</tr>
						<tr>
							<td colspan="3" class="bdrbtm" style="width:485px;">';
							if($signNurseStatus=="Yes"){
								$table_pdf_new.="<br><b>Nurse:&nbsp;</b>".$signNurseLastName.','.$signNurseFirstName;
								$table_pdf_new.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$table_pdf_new.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($ViewgenAnesNurseRow["signNurseDateTime"]);
							}else {
								$table_pdf_new.="<br><b>Nurse:&nbsp;</b>________";
								$table_pdf_new.="<br><b>Electronically Signed:&nbsp;</b>________";
								$table_pdf_new.="<br><b>Signature Date:&nbsp;</b>________";
							}
							$table_pdf_new.='
							</td>
							<td class="bdrbtm" style="width:250px;"><b>Relief Nurse:&nbsp;</b>';
							if($relivedNurseId && $userNameArr[$relivedNurseId]){
								$table_pdf_new.=$userNameArr[$relivedNurseId];
							}else{
								$table_pdf_new.="_____";
							}
							$table_pdf_new.='
							</td>
						</tr>
					</table>
				</td>
			</tr>			
			';
		$table_pdf_new.='	
		</table></page>';
		$table.=$table_pdf_new;
}
//============================================================================//
//====================Laser Procedure ========================================//

//select the detail from database

	$ViewlaserprocedureQry = "select *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat from `laser_procedure_patient_table` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$ViewlaserprocedureRes = imw_query($ViewlaserprocedureQry) or die(imw_error()); 
	$ViewlaserprocedueNumRow = imw_num_rows($ViewlaserprocedureRes);
	$ViewlaserprocedureRow = imw_fetch_array($ViewlaserprocedureRes); 
	$ViewlaserprocedureID=$ViewlaserprocedureRow['laser_procedureRecordID'];

//SHOW DETAIL OF PATIENT PRE OP MEDICATION
	$preOpPhysicianOrdersId = $_REQUEST['preOpPhysicianOrdersId'];	//
	$laserProcDetailsQry = "select * from preopphysicianorders where patient_confirmation_id = '".$_REQUEST["pConfId"]."'";
	$laserProcDetailsRes = imw_query($laserProcDetailsQry) or die(imw_error());
	$laserProcDetailsNumRow = imw_num_rows($laserProcDetailsRes);
	if($laserProcDetailsNumRow>0) {
		$laserProcDetailsRow = imw_fetch_array($laserProcDetailsRes);
		$prefilMedicationStatus = $laserProcDetailsRow['prefilMedicationStatus'];
		$preOpPhysicianOrdersId=$laserProcDetailsRow['preOpPhysicianOrdersId'];
	}

	$preOpPatientDetails = $objManageData->getArrayRecords('patientpreopmedication_tbl', 'patient_confirmation_id', $_REQUEST["pConfId"],'patientPreOpMediId','ASC');
//SHOW DETAIL OF PATIENT PRE OP MEDICATION
		
		//HISTORY
		$laserProcFormStatus=$ViewlaserprocedureRow['form_status'];
		$laser_chk_chief_complaint=$ViewlaserprocedureRow['chk_laser_chief_complaint'];
		$laser_chief_complaint_detail=stripslashes($ViewlaserprocedureRow['laser_chief_complaint']);
		$laser_chk_present_illness_hx=$ViewlaserprocedureRow['chk_laser_present_illness_hx'];
		$laser_present_illness_hx_detail=stripslashes($ViewlaserprocedureRow['laser_present_illness_hx']);
		$laser_chk_past_med_hx=$ViewlaserprocedureRow['chk_laser_past_med_hx'];
		$laser_past_med_hx_detail=stripslashes($ViewlaserprocedureRow['laser_past_med_hx']);
		$laser_chk_medication=$ViewlaserprocedureRow['chk_laser_medication'];
		$laser_medication_detail=stripslashes($ViewlaserprocedureRow['laser_medication']);
		$allergies_status_laser=stripslashes($ViewlaserprocedureRow['allergies_status_reviewed']);
//new	
		$verified_nurseID =$ViewlaserprocedureRow['verified_nurse_Id'];
		$verified_nurseName =$ViewlaserprocedureRow['verified_nurse_name'];
		$verified_nurseStatus=$ViewlaserprocedureRow['verified_nurse_Status'];

		$verified_surgeonID =$ViewlaserprocedureRow['verified_surgeon_Id'];
		$verified_surgeonName =$ViewlaserprocedureRow['verified_surgeon_Name'];
		$verified_surgeonStatus=$ViewlaserprocedureRow['verified_surgeon_Status'];
//new end	
		
		$stable_chbx=stripslashes($ViewlaserprocedureRow['stable_chbx']);
		$stable_other_chbx=stripslashes($ViewlaserprocedureRow['stable_other_chbx']);
		$stable_other_txtbx=stripslashes($ViewlaserprocedureRow['stable_other_txtbx']);
		$best_correction_vision_R=stripslashes($ViewlaserprocedureRow['best_correction_vision_R']);
		$best_correction_vision_L=stripslashes($ViewlaserprocedureRow['best_correction_vision_L']);
		$glare_acuity_R=$ViewlaserprocedureRow['glare_acuity_R'];
		$glare_acuity_L=$ViewlaserprocedureRow['glare_acuity_L'];
		$laser_sle_detail=stripslashes($ViewlaserprocedureRow['laser_sle']);
		$laser_fundus_exam_detail=stripslashes($ViewlaserprocedureRow['laser_fundus_exam']);
		$laser_mental_state_detail=stripslashes($ViewlaserprocedureRow['laser_mental_state']);
		
		
		//$allergies_status_reviewed=$ViewlaserprocedureRow['chbx_drug_react_reviewed'];
		
		$pre_laser_IOP_R=stripslashes($ViewlaserprocedureRow['pre_laser_IOP_R']);
		$pre_laser_IOP_L=stripslashes($ViewlaserprocedureRow['pre_laser_IOP_L']);
		$pre_iop_na		=stripslashes($ViewlaserprocedureRow['pre_iop_na']);
//laser
		$pre_laser_IOP_na=stripslashes($ViewlaserprocedureRow['pre_iop_na']);
		
		$laser_other=stripslashes($ViewlaserprocedureRow['laser_other']);

		$verified_nurseTimeout	=	$ViewlaserprocedureRow['verified_nurse_timeout'];
		if($verified_nurseTimeout <> '0000-00-00 00:00:00' && !empty($verified_nurseTimeout)){
			//$verified_nurseTimeout=date('h:i A',strtotime($verified_nurseTimeout));
			$verified_nurseTimeout=$objManageData->getTmFormat($verified_nurseTimeout);
		}else{
			$verified_nurseTimeout	=	'';	
		}
		
		$verified_surgeonTimeout	=	$ViewlaserprocedureRow['verified_surgeon_timeout'];
		if($verified_surgeonTimeout <> '0000-00-00 00:00:00' && !empty($verified_surgeonTimeout)){
			//$verified_surgeonTimeout=date('h:i A',strtotime($verified_surgeonTimeout));
			$verified_surgeonTimeout=$objManageData->getTmFormat($verified_surgeonTimeout);
		}else{
			$verified_surgeonTimeout	=	'';	
		}

		$asa_status	=	$ViewlaserprocedureRow['asa_status'];
		$prelaserVitalSignTime	=	$ViewlaserprocedureRow['prelaserVitalSignTime'];
		if($prelaserVitalSignTime <> '0000-00-00 00:00:00' && !empty($prelaserVitalSignTime)){
			//$prelaserVitalSignTime=date('h:i A',strtotime($prelaserVitalSignTime));
			$prelaserVitalSignTime=$objManageData->getTmFormat($prelaserVitalSignTime);
		}else{
			$prelaserVitalSignTime	=	'';	
		}
		
		$postlaserVitalSignTime	=	$ViewlaserprocedureRow['postlaserVitalSignTime'];
		if($postlaserVitalSignTime <> '0000-00-00 00:00:00' && !empty($postlaserVitalSignTime)){
			//$postlaserVitalSignTime=date('h:i A',strtotime($postlaserVitalSignTime));
			$postlaserVitalSignTime=$objManageData->getTmFormat($postlaserVitalSignTime);
		}else{
			$postlaserVitalSignTime	=	'';	
		}

		$proc_start_time	=	$ViewlaserprocedureRow['proc_start_time'];
		if($proc_start_time <> '0000-00-00 00:00:00' && !empty($proc_start_time)){
			//$proc_start_time=date('h:i A',strtotime($proc_start_time));
			$proc_start_time=$objManageData->getTmFormat($proc_start_time);
		}else{
			$proc_start_time	=	'';	
		}
		$proc_end_time	=	$ViewlaserprocedureRow['proc_end_time'];
		if($proc_end_time <> '0000-00-00 00:00:00' && !empty($proc_end_time)){
			//$proc_end_time=date('h:i A',strtotime($proc_end_time));
			$proc_end_time=$objManageData->getTmFormat($proc_end_time);
		}else{
			$proc_end_time	=	'';	
		}
		
//new
		$laser_pre_op_diagnosis=stripslashes($ViewlaserprocedureRow['pre_op_diagnosis']);//
//new end

//medication
		$laser_other_pre_medication=stripslashes($ViewlaserprocedureRow['laser_other_pre_medication']);
		$laser_comments=stripslashes($ViewlaserprocedureRow['laser_comments']);

//pre vital
		$chk_laser_patient_evaluated=stripslashes($ViewlaserprocedureRow['chk_laser_patient_evaluated']);
		$prelaserVitalSignBP=stripslashes($ViewlaserprocedureRow['prelaserVitalSignBP']);
		$prelaserVitalSignP=stripslashes($ViewlaserprocedureRow['prelaserVitalSignP']);
		$prelaserVitalSignR=stripslashes($ViewlaserprocedureRow['prelaserVitalSignR']);
		
		$laser_chk_spot_duration=$ViewlaserprocedureRow['chk_laser_spot_duration'];
		$laser_spot_duration_detail=stripslashes($ViewlaserprocedureRow['laser_spot_duration']);
		
		$laser_chk_spot_size=$ViewlaserprocedureRow['chk_laser_spot_size'];
		$laser_spot_size_detail=stripslashes($ViewlaserprocedureRow['laser_spot_size']);
		
		$laser_chk_power=$ViewlaserprocedureRow['chk_laser_power'];
		$laser_power_detail=stripslashes($ViewlaserprocedureRow['laser_power']);
		
		$laser_chk_shots=$ViewlaserprocedureRow['chk_laser_shots'];
		$laser_shots_detail=stripslashes($ViewlaserprocedureRow['laser_shots']);
		
		$laser_chk_total_energy=$ViewlaserprocedureRow['chk_laser_total_energy'];
		$laser_total_energy_detail=stripslashes($ViewlaserprocedureRow['laser_total_energy']);
		
		$laser_chk_degree_of_opening=$ViewlaserprocedureRow['chk_laser_degree_of_opening'];
		$laser_degree_of_opening_detail=stripslashes($ViewlaserprocedureRow['laser_degree_of_opening']);
		
		$laser_chk_exposure=$ViewlaserprocedureRow['chk_laser_exposure'];
		$laser_exposure_detail=stripslashes($ViewlaserprocedureRow['laser_exposure']);

		$laser_chk_count=$ViewlaserprocedureRow['chk_laser_count'];
		$laser_count_detail=stripslashes($ViewlaserprocedureRow['laser_count']);
		
		$laser_post_progress_detail=stripslashes($ViewlaserprocedureRow['laser_post_progress']);							
		$laser_post_operative_detail=stripslashes($ViewlaserprocedureRow['laser_post_operative']);
		
		
		//post vital
		$postlaserVitalSignBP=stripslashes($ViewlaserprocedureRow['postlaserVitalSignBP']);
		$postlaserVitalSignP=stripslashes($ViewlaserprocedureRow['postlaserVitalSignP']);
		$postlaserVitalSignR=stripslashes($ViewlaserprocedureRow['postlaserVitalSignR']);
//laser
		$iop_pressure_l=stripslashes($ViewlaserprocedureRow['iop_pressure_l']);
		$iop_pressure_r=stripslashes($ViewlaserprocedureRow['iop_pressure_r']);
		$iop_pressure_na=stripslashes($ViewlaserprocedureRow['iop_na']);
//laser

//new
		$post_comment=stripslashes($ViewlaserprocedureRow['post_op_operative_comment']);
//new end
		//surgeon sign
		$signSurgeon1Id =stripslashes($ViewlaserprocedureRow['signSurgeon1Id']);
		$signSurgeon1FirstName =stripslashes($ViewlaserprocedureRow['signSurgeon1FirstName']);
		$signSurgeon1MiddleName =stripslashes($ViewlaserprocedureRow['signSurgeon1MiddleName']);
		$signSurgeon1LastName =stripslashes($ViewlaserprocedureRow['signSurgeon1LastName']);
		$signSurgeon1Status =stripslashes($ViewlaserprocedureRow['signSurgeon1Status']);
		$signSurgeonName = stripslashes($signSurgeon1LastName).", ".stripslashes($signSurgeon1FirstName)." ".stripslashes($signSurgeon1MiddleName);
	
		//nurse sign
		$signNurseId =stripslashes($ViewlaserprocedureRow['signNurseId']);
		$signNurseFirstName =stripslashes($ViewlaserprocedureRow['signNurseFirstName']);
		$signNurseMiddleName =stripslashes($ViewlaserprocedureRow['signNurseMiddleName']);
		$signNurseLastName =stripslashes($ViewlaserprocedureRow['signNurseLastName']);
		$signNurseStatus =stripslashes($ViewlaserprocedureRow['signNurseStatus']);
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
		
		$discharge_home = (int)$ViewlaserprocedureRow['discharge_home'];
		$patients_relation = stripslashes($ViewlaserprocedureRow['patients_relation']);
		$patients_relation_other = stripslashes($ViewlaserprocedureRow['patients_relation_other']);
		$patient_transfer = (int)$ViewlaserprocedureRow['patient_transfer'];
		$discharge_time = $ViewlaserprocedureRow['discharge_time'];
		$discharge_time = ($discharge_time && $discharge_time <> '0000-00-00 00:00:00') ? $objManageData->getTmFormat($discharge_time) : '';
		$version_num_laser_proc = (int)$ViewlaserprocedureRow['version_num'];
		
		//Image
		$laser_procedure_image =stripslashes($ViewlaserprocedureRow['laser_procedure_image']);
		$laser_procedure_image_path =stripslashes($ViewlaserprocedureRow['laser_procedure_image_path']);
//select the detail from database

	if($laserProcFormStatus=='completed' || $laserProcFormStatus=='not completed')
		{
			$table_print ='';
			$table_print.='<page>'.$head_table;
			if(file_exists('laser_procedure_printpop_common.php'))
			{
				include'laser_procedure_printpop_common.php';
			}
			$table_print.='</page>';
			$table.=$table_print;
		}	

//===========================================================================//


//=====================================Transfer & Followup Record=====================================//
if(file_exists("injection_misc_pdf_content.php")){
	$table_main	=	"";
	include_once("injection_misc_pdf_content.php");
	if($table_main){$table.='<page>'.$table_main.'</page>';}
}
//===========================================================================//


//==================Operating Room Record====================================//

$OpRoom_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$pConfId."'";
$OpRoom_patientConfirm_tblRes = imw_query($OpRoom_patientConfirm_tblQry) or die(imw_error());
$OpRoom_patientConfirm_tblRow = imw_fetch_array($OpRoom_patientConfirm_tblRes);
if(!$patient_id) {
	$patient_id = $OpRoom_patientConfirm_tblRow["patientId"];
}	

if(!$patient_id) {
	$patient_id = $_SESSION['patient_id'];
}	


$medsQry=("select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_prescription_medication_tbl where confirmation_id=$pConfId ORDER BY prescription_medication_name");
$medsRes = imw_query($medsQry);
$medsnum=@imw_num_rows($medsRes);
$l=0;
/*while($detailsMeds =@imw_fetch_array($medsRes))
{
   foreach($detailsMeds as $key =>$val){
   $detailsmed[$l][$key] = $val;
   }
   $l++;
}*/
//print_r($detailsmed);

//
$ViewLoginUserNameQry = "select * from `users` where  usersId = '".$_SESSION["loginUserId"]."'";
									$ViewLoginUserNameRes = imw_query($ViewLoginUserNameQry) or die(imw_error()); 
									$ViewLoginUserNameRow = imw_fetch_array($ViewLoginUserNameRes); 
									
									$loggedInUserName = $ViewLoginUserNameRow["lname"].", ".$ViewLoginUserNameRow["fname"]." ".$ViewLoginUserNameRow["mname"];
									$loggedInUserType = $ViewLoginUserNameRow["user_type"];
									$loggedInSignatureOfUser = $ViewLoginUserNameRow["signature"];

//
//CODE RELATED TO NURSE SIGNATURE ON FILE
																if($loggedInUserType<>"Nurse") {
																	$loginUserName = $_SESSION['loginUserName'];
																	$callJavaFun = "return noAuthorityFun('Nurse');";
																	$callJavaFunNurse1 = "return noAuthorityFun('Nurse');";
																//}else if ($loggedInUserType=="Nurse" && !$loggedInSignatureOfUser) {
																	//$callJavaFun = "return noSignInAdmin();";
																	//$callJavaFunNurse1 = "return noSignInAdmin();";
																}else {
																	$loginUserId = $_SESSION["loginUserId"];
																	$callJavaFun = "document.frm_op_room.hiddSignatureId.value='TDnurseSignatureId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse1');";
																	$callJavaFunNurse1 = "document.frm_op_room.hiddSignatureId.value='TDnurse1SignatureId'; return displaySignature('TDnurse1NameId','TDnurse1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse2');";
																}
															
																$signOnFileStatus = "Yes";
																$TDnurseNameIdDisplay = "block";
																$TDnurseSignatureIdDisplay = "none";
																$NurseNameShow = $loggedInUserName;
																$Nurse1NameShow = $loggedInUserName;
																
																if($signNurseId<>0 && $signNurseId<>"") {
																	$NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
																	$signOnFileStatus = $signNurseStatus;	
																	
																	$TDnurseNameIdDisplay = "none";
																	$TDnurseSignatureIdDisplay = "block";
																}
																//CODE TO REMOVE NURSE SIGNATURE
																	if($_SESSION["loginUserId"]==$signNurseId) {
																		$callJavaFunDel = "document.frm_op_room.hiddSignatureId.value='TDnurseNameId'; return displaySignature('TDnurseNameId','TDnurseSignatureId','op_room_record_ajaxSign.php','$loginUserId','Nurse1','delSign');";
																	}else {
																		$callJavaFunDel = "alert('Only $NurseNameShow can remove this signature');";
																	}	
																//END CODE TO REMOVE NURSE SIGNATURE	
																	
															//END CODE RELATED TO NURSE SIGNATURE ON FILE
															
													 //CODE RELATED TO SURGEON 1 SIGNATURE ON FILE AT THE BOTTOM
							if($loggedInUserType<>"Surgeon") {
								
								$loginUserName = $_SESSION['loginUserName'];
								$callJavaFunSurgeon2 = "return noAuthorityFun('Surgeon');";
							
							//}else if ($loggedInUserType=="Surgeon" && !$loggedInSignatureOfUser) {
								//$callJavaFunSurgeon2 = "return noSignInAdmin();";
							}else if ($loggedInUserType=="Surgeon" && $_SESSION["loginUserId"]==$signSurgeon2Id) {
								$callJavaFunSurgeon2 = "return alreadySignOnce('Surgeon2');";
							}else {
								$loginUserId = $_SESSION["loginUserId"];
								$callJavaFunSurgeon2 = "document.frm_op_room.hiddSignatureId.value='TDsurgeon2SignatureId'; return displaySignature('TDsurgeon2NameId','TDsurgeon2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon2');";
							}					
							$surgeon2SignOnFileStatus = "Yes";
							$TDsurgeon2NameIdDisplay = "block";
							$TDsurgeon2SignatureIdDisplay = "none";
							$Surgeon2Name = $loggedInUserName;
							if($signSurgeon2Id<>0 && $signSurgeon2Id<>"") {
								$Surgeon2Name = $signSurgeon2LastName.", ".$signSurgeon2FirstName." ".$signSurgeon2MiddleName;
								$surgeon2SignOnFileStatus = $signSurgeon2Status;	
								
								$TDsurgeon2NameIdDisplay = "none";
								$TDsurgeon2SignatureIdDisplay = "block";
							}
							//CODE TO REMOVE SURGEON 2 SIGNATURE	
								if($_SESSION["loginUserId"]==$signSurgeon2Id) {
									$callJavaFunSurgeon2Del = "document.frm_op_room.hiddSignatureId.value='TDsurgeon2NameId'; return displaySignature('TDsurgeon2NameId','TDsurgeon2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon2','delSign');";
								}else {
									$callJavaFunSurgeon2Del = "alert('Only Dr. $Surgeon2Name can remove this signature');";
								}
							//END CODE TO REMOVE SURGEON 2 SIGNATURE
						//END CODE RELATED TO SURGEON 1 SIGNATURE ON FILE AT THE BOTTOM
						
						//CODE RELATED TO SURGEON 2 SIGNATURE ON FILE AT THE BOTTOM
							if($loggedInUserType<>"Surgeon") {
								
								$loginUserName = $_SESSION['loginUserName'];
								$callJavaFunSurgeon3 = "return noAuthorityFun('Surgeon');";
							
							//}else if ($loggedInUserType=="Surgeon" && !$loggedInSignatureOfUser) {
								//$callJavaFunSurgeon3 = "return noSignInAdmin();";
							}else if ($loggedInUserType=="Surgeon" && $_SESSION["loginUserId"]==$signSurgeon1Id) {
								$callJavaFunSurgeon3 = "return alreadySignOnce('Surgeon1');";
							}else {
								$loginUserId = $_SESSION["loginUserId"];
								$callJavaFunSurgeon3 = "document.frm_op_room.hiddSignatureId.value='TDsurgeon3SignatureId'; return displaySignature('TDsurgeon3NameId','TDsurgeon3SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon3');";
							}					
							$surgeon3SignOnFileStatus = "Yes";
							$TDsurgeon3NameIdDisplay = "block";
							$TDsurgeon3SignatureIdDisplay = "none";
							$Surgeon3Name = $loggedInUserName;
							if($signSurgeon3Id<>0 && $signSurgeon3Id<>"") {
								$Surgeon3Name = $signSurgeon3LastName.", ".$signSurgeon3FirstName." ".$signSurgeon3MiddleName;
								$surgeon3SignOnFileStatus = $signSurgeon3Status;	
								
								$TDsurgeon3NameIdDisplay = "none";
								$TDsurgeon3SignatureIdDisplay = "block";
							}
							//CODE TO REMOVE SURGEON 3 SIGNATURE	
								if($_SESSION["loginUserId"]==$signSurgeon3Id) {
									$callJavaFunSurgeon3Del = "document.frm_op_room.hiddSignatureId.value='TDsurgeon3NameId'; return displaySignature('TDsurgeon3NameId','TDsurgeon3SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon3','delSign');";
								}else {
									$callJavaFunSurgeon3Del = "alert('Only Dr. $Surgeon3Name can remove this signature');";
								}
							//END CODE TO REMOVE SURGEON 3 SIGNATURE
						//END CODE RELATED TO SURGEON 2 SIGNATURE ON FILE AT THE BOTTOM
								
															
															//CODE RELATED TO SURGEON SIGNATURE ON FILE
																if($loggedInUserType<>"Surgeon") {
																	
																	$loginUserName = $_SESSION['loginUserName'];
																	$callJavaFunSurgeon = "return noAuthorityFun('Surgeon');";
																
																//}else if ($loggedInUserType=="Surgeon" && !$loggedInSignatureOfUser) {
																	//$callJavaFunSurgeon = "return noSignInAdmin();";
																}else {
																
																	$loginUserId = $_SESSION["loginUserId"];
																	$callJavaFunSurgeon = "document.frm_op_room.hiddSignatureId.value='TDsurgeon1SignatureId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon1');";
																}					
																$surgeon1SignOnFileStatus = "Yes";
																$TDsurgeon1NameIdDisplay = "block";
																$TDsurgeon1SignatureIdDisplay = "none";
																$Surgeon1Name = $loggedInUserName;
																if($signSurgeon1Id<>0 && $signSurgeon1Id<>"") {
																	$Surgeon1Name = $signSurgeon1LastName.", ".$signSurgeon1FirstName." ".$signSurgeon1MiddleName;
																	$surgeon1SignOnFileStatus = $signSurgeon1Status;	
																	
																	$TDsurgeon1NameIdDisplay = "none";
																	$TDsurgeon1SignatureIdDisplay = "block";
																}
																//CODE TO REMOVE SURGEON 1 SIGNATURE	
																	if($_SESSION["loginUserId"]==$signSurgeon1Id) {
																		$callJavaFunSurgeonDel = "document.frm_op_room.hiddSignatureId.value='TDsurgeon1NameId'; return displaySignature('TDsurgeon1NameId','TDsurgeon1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Surgeon1','delSign');";
																	}else {
																		$callJavaFunSurgeonDel = "alert('Only Dr. $Surgeon1Name can remove this signature');";
																	}
																//END CODE TO REMOVE SURGEON 1 SIGNATURE	
															//END CODE RELATED TO SURGEON SIGNATURE ON FILE
															
															//CODE RELATED TO ANESTHESIOLOGIST
																if($loggedInUserType<>"Anesthesiologist") {
																	$loginUserName = $_SESSION['loginUserName'];
																	$callJavaFunAnes = "return noAuthorityFun('Anesthesiologist');";
																	$callJavaFunAnes2 = "return noAuthorityFun('Anesthesiologist');";
																//}else if ($loggedInUserType=="Anesthesiologist" && !$loggedInSignatureOfUser) {
																	//$callJavaFunAnes = "return noSignInAdmin();";
																	//$callJavaFunAnes2 = "return noSignInAdmin();";
																}else {
																	$loginUserId = $_SESSION["loginUserId"];
																	$callJavaFunAnes = "document.frm_op_room.hiddSignatureId.value='TDanesthesia1SignatureId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia1');";
																	$callJavaFunAnes2 = "document.frm_op_room.hiddSignatureId.value='TDanesthesia2SignatureId'; return displaySignature('TDanesthesia2NameId','TDanesthesia2SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia2');";
																	
																}
															
																
																$anesthesia1SignOnFileStatus = "Yes";
																$TDanesthesia1NameIdDisplay = "block";
																$TDanesthesia1SignatureIdDisplay = "none";
																$Anesthesia1Name = $loggedInUserName;
																
																if($signAnesthesia1Id<>0 && $signAnesthesia1Id<>"") {
																	$Anesthesia1Name = $signAnesthesia1LastName." ".$signAnesthesia1FirstName." ".$signAnesthesia1MiddleName;
																	$anesthesia1SignOnFileStatus = $signAnesthesia1Status;	
																	
																	$TDanesthesia1NameIdDisplay = "none";
																	$TDanesthesia1SignatureIdDisplay = "block";
																}
																

//


$preOpDiagnosis = $ViewOpRoomRecordRow['preOpDiagnosis'];

$operativeProcedures= $ViewOpRoomRecordRow['operativeProcedures'];

$postOpDiagnosis = $ViewOpRoomRecordRow['postOpDiagnosis'];

$bssValue= $ViewOpRoomRecordRow['bssValue'];

$infusionBottle = $ViewOpRoomRecordRow['infusionBottle'];
$Epinephrine03= $ViewOpRoomRecordRow['Epinephrine03'];
$Vancomycin01= $ViewOpRoomRecordRow['Vancomycin01'];
$Vancomycin02= $ViewOpRoomRecordRow['Vancomycin02'];
$omidria	= $ViewOpRoomRecordRow['omidria'];
$InfusionOtherChk= $ViewOpRoomRecordRow['InfusionOtherChk'];
$infusionBottleOther= stripslashes($ViewOpRoomRecordRow['infusionBottleOther']);
//
		$Solumedrol = $ViewOpRoomRecordRow["Solumedrol"];
		$Dexamethasone = $ViewOpRoomRecordRow["Dexamethasone"];
		$Kenalog = $ViewOpRoomRecordRow["Kenalog"];
		$Vancomycin = $ViewOpRoomRecordRow["Vancomycin"];
		$Trimaxi = $ViewOpRoomRecordRow["Trimaxi"];
		$injXylocaineMPF = $ViewOpRoomRecordRow["injXylocaineMPF"];
		$injMiostat = $ViewOpRoomRecordRow["injMiostat"];
		$PhenylLido = $ViewOpRoomRecordRow["PhenylLido"];
		$Ancef = $ViewOpRoomRecordRow["Ancef"];
		$Gentamicin = $ViewOpRoomRecordRow["Gentamicin"];
		$Depomedrol = $ViewOpRoomRecordRow["Depomedrol"];
		$postOpInjOther = $ViewOpRoomRecordRow["postOpInjOther"];
		
		$SolumedrolList = $ViewOpRoomRecordRow["SolumedrolList"];
		$DexamethasoneList = $ViewOpRoomRecordRow["DexamethasoneList"];
		$KenalogList = $ViewOpRoomRecordRow["KenalogList"];
		$VancomycinList = $ViewOpRoomRecordRow["VancomycinList"];
		$TrimaxiList = $ViewOpRoomRecordRow["TrimaxiList"];
		$injXylocaineMPFList = $ViewOpRoomRecordRow["injXylocaineMPFList"];
		$injMiostatList = $ViewOpRoomRecordRow["injMiostatList"];
		$PhenylLidoList = $ViewOpRoomRecordRow["PhenylLidoList"];
		$AncefList = $ViewOpRoomRecordRow["AncefList"];
		$GentamicinList = $ViewOpRoomRecordRow["GentamicinList"];
		$DepomedrolList = $ViewOpRoomRecordRow["DepomedrolList"];
		
		$anesthesia_service = $ViewOpRoomRecordRow["anesthesia_service"];
		
		$TopicalBlock = $ViewOpRoomRecordRow["TopicalBlock"];
		
		
		$patch = $ViewOpRoomRecordRow["patch"];
		$shield = $ViewOpRoomRecordRow["shield"];
		$needleSutureCount = $ViewOpRoomRecordRow["needleSutureCount"];
		$needleSutureCountNA = $ViewOpRoomRecordRow["needleSutureCountNA"];
		
		$collagenShield = $ViewOpRoomRecordRow["collagenShield"];
		$Econopred= $ViewOpRoomRecordRow['Econopred'];
		$Zymar= $ViewOpRoomRecordRow['Zymar'];
		$Tobradax= $ViewOpRoomRecordRow['Tobradax'];
		$soakedInOtherChk = $ViewOpRoomRecordRow['soakedInOtherChk'];
		$soakedInOther= $ViewOpRoomRecordRow['soakedInOther'];		
		$postOpDiagnosis = $ViewOpRoomRecordRow["postOpDiagnosis"];
		
		if(trim($postOpDiagnosis)=="") {
			$postOpDiagnosis = $preOpDiagnosis;
		}
		
		$other_remain = $ViewOpRoomRecordRow["other_remain"];
		$postOpDrops = $ViewOpRoomRecordRow["postOpDrops"]; //SEE THIS AT THE BOTTOM
		$complications = $ViewOpRoomRecordRow["complications"];
		$intraOpPostOpOrders = $ViewOpRoomRecordRow['intraOpPostOpOrder'];
		$nurseNotes = $ViewOpRoomRecordRow["nurseNotes"];
		$others_present = $ViewOpRoomRecordRow["others_present"];
		$opRoomFormStatus = $ViewOpRoomRecordRow["form_status"];
		
		$surgeonId1 = $ViewOpRoomRecordRow["surgeonId1"];
		$anesthesiologistId = $ViewOpRoomRecordRow["anesthesiologistId"];
		$scrubTechId1 = $ViewOpRoomRecordRow["scrubTechId1"];
		$scrubTechId2 = $ViewOpRoomRecordRow["scrubTechId2"];
		$circulatingNurseId = $ViewOpRoomRecordRow["circulatingNurseId"];
		$RcNurse			=	$ViewOpRoomRecordRow["nurseTitle"];
		$NurseId = $ViewOpRoomRecordRow["nurseId"];
		//$signOnFileSurgeon1 = $ViewOpRoomRecordRow["signOnFileSurgeon1"];

		//$signOnFileScrubTech1 = $ViewOpRoomRecordRow["signOnFileScrubTech1"];
		//$signOnFileScrubTech2 = $ViewOpRoomRecordRow["signOnFileScrubTech2"];
		//$signOnFileCirculatingNurse = $ViewOpRoomRecordRow["signOnFileCirculatingNurse"];
		//$signOnFileRelievedBy = $ViewOpRoomRecordRow["signOnFileRelievedBy"];
	
		$iolName = $ViewOpRoomRecordRow["iol_ScanUpload"];
		
		$signNurseId = $ViewOpRoomRecordRow["signNurseId"];
		$signNurseFirstName = $ViewOpRoomRecordRow["signNurseFirstName"];
		$signNurseMiddleName = $ViewOpRoomRecordRow["signNurseMiddleName"];
		$signNurseLastName = $ViewOpRoomRecordRow["signNurseLastName"];
		$signNurseName = $ViewOpRoomRecordRow["signNurseLastName"].','. $ViewOpRoomRecordRow["signNurseFirstName"];
		$signNurseStatus = $ViewOpRoomRecordRow["signNurseStatus"];
		
		
		$signNurse1Id = $ViewOpRoomRecordRow["signNurse1Id"];
		$signNurse1FirstName = $ViewOpRoomRecordRow["signNurse1FirstName"];
		$signNurse1MiddleName = $ViewOpRoomRecordRow["signNurse1MiddleName"];
		$signNurse1LastName = $ViewOpRoomRecordRow["signNurse1LastName"];
		$signNurse1Name = $ViewOpRoomRecordRow["signNurse1LastName"].','.$ViewOpRoomRecordRow["signNurse1FirstName"];
		$signNurse1Status = $ViewOpRoomRecordRow["signNurse1Status"];
		
		$signSurgeon1Id = $ViewOpRoomRecordRow["signSurgeon1Id"];
		$signSurgeon1FirstName = $ViewOpRoomRecordRow["signSurgeon1FirstName"];
		$signSurgeon1MiddleName = $ViewOpRoomRecordRow["signSurgeon1MiddleName"];
		$signSurgeon1LastName = $ViewOpRoomRecordRow["signSurgeon1LastName"];
		$signSurgeon1Name = $ViewOpRoomRecordRow["signSurgeon1LastName"].','.$ViewOpRoomRecordRow["signSurgeon1FirstName"];
		$signSurgeon1Status = $ViewOpRoomRecordRow["signSurgeon1Status"];
		
		$signSurgeon2Id = $ViewOpRoomRecordRow["signSurgeon2Id"];
		$signSurgeon2FirstName = $ViewOpRoomRecordRow["signSurgeon2FirstName"];
		$signSurgeon2MiddleName = $ViewOpRoomRecordRow["signSurgeon2MiddleName"];
		$signSurgeon2LastName = $ViewOpRoomRecordRow["signSurgeon2LastName"];
		$signSurgeon2Name = $ViewOpRoomRecordRow["signSurgeon2LastName"].','.$ViewOpRoomRecordRow["signSurgeon2FirstName"];
		$signSurgeon2Status = $ViewOpRoomRecordRow["signSurgeon2Status"];
	
		$signSurgeon3Id = $ViewOpRoomRecordRow["signSurgeon3Id"];
		$signSurgeon3FirstName = $ViewOpRoomRecordRow["signSurgeon3FirstName"];
		$signSurgeon3MiddleName = $ViewOpRoomRecordRow["signSurgeon3MiddleName"];
		$signSurgeon3LastName = $ViewOpRoomRecordRow["signSurgeon3LastName"];
		$signSurgeon3Name = $ViewOpRoomRecordRow["signSurgeon3LastName"].','.$ViewOpRoomRecordRow["signSurgeon3FirstName"];
		$signSurgeon3Status = $ViewOpRoomRecordRow["signSurgeon3Status"];
	
		$signAnesthesia1Id = $ViewOpRoomRecordRow["signAnesthesia1Id"];
		$signAnesthesia1FirstName = $ViewOpRoomRecordRow["signAnesthesia1FirstName"];
		$signAnesthesia1MiddleName = $ViewOpRoomRecordRow["signAnesthesia1MiddleName"];
		$signAnesthesia1LastName = $ViewOpRoomRecordRow["signAnesthesia1LastName"];
		$signAnesthesia1Name = $ViewOpRoomRecordRow["signAnesthesia1LastName"].','.$ViewOpRoomRecordRow["signAnesthesia1FirstName"];
		$signAnesthesia1Status = $ViewOpRoomRecordRow["signAnesthesia1Status"];

		$signAnesthesia2Id = $ViewOpRoomRecordRow["signAnesthesia2Id"];
		$signAnesthesia2FirstName = $ViewOpRoomRecordRow["signAnesthesia2FirstName"];
		$signAnesthesia2MiddleName = $ViewOpRoomRecordRow["signAnesthesia2MiddleName"];
		$signAnesthesia2LastName = $ViewOpRoomRecordRow["signAnesthesia2LastName"];
		$signAnesthesia2Name = $ViewOpRoomRecordRow["signAnesthesia2LastName"].','.$ViewOpRoomRecordRow["signAnesthesia2FirstName"];
		$signAnesthesia2Status = $ViewOpRoomRecordRow["signAnesthesia2Status"];
	
		$signScrubTech1Id = $ViewOpRoomRecordRow["signScrubTech1Id"];
		$signScrubTech1FirstName = $ViewOpRoomRecordRow["signScrubTech1FirstName"];
		$signScrubTech1MiddleName = $ViewOpRoomRecordRow["signScrubTech1MiddleName"];
		$signScrubTech1LastName = $ViewOpRoomRecordRow["signScrubTech1LastName"];
		$signScrubTech1Name= $ViewOpRoomRecordRow["signScrubTech1LastName"].','. $ViewOpRoomRecordRow["signScrubTech1FirstName"];
		$signScrubTech1Status = $ViewOpRoomRecordRow["signScrubTech1Status"];
	
		$signScrubTech2Id = $ViewOpRoomRecordRow["signScrubTech2Id"];
		$signScrubTech2FirstName = $ViewOpRoomRecordRow["signScrubTech2FirstName"];
		$signScrubTech2MiddleName = $ViewOpRoomRecordRow["signScrubTech2MiddleName"];
		$signScrubTech2LastName = $ViewOpRoomRecordRow["signScrubTech2LastName"];
		$signScrubTech2Name= $ViewOpRoomRecordRow["signScrubTech2LastName"].','. $ViewOpRoomRecordRow["signScrubTech2FirstName"];
		$signScrubTech2Status = $ViewOpRoomRecordRow["signScrubTech2Status"];
		
		$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
		$patientIdentityVerified = $ViewOpRoomRecordRow["patientIdentityVerified"];
		$siteVerified = $ViewOpRoomRecordRow["siteVerified"];
		$procedurePrimaryVerified = $ViewOpRoomRecordRow["procedurePrimaryVerified"];
		$anesthesiologist = $ViewOpRoomRecordRow["anesthesiologist"];
		$verifiedbyNurseName = $ViewOpRoomRecordRow["verifiedbyNurseName"];
		$verifiedbyNurse = $ViewOpRoomRecordRow["verifiedbyNurse"];
		//$verifiedbyNurseTime = $ViewOpRoomRecordRow["verifiedbyNurseTime"];
		$verifiedbyNurseTime = $objManageData->getTmFormat($ViewOpRoomRecordRow["verifiedbyNurseTime"]);
		$verifiedbySurgeon = $ViewOpRoomRecordRow["verifiedbySurgeon"];
		$verifiedbyAnesthesiologist = $ViewOpRoomRecordRow["verifiedbyAnesthesiologist"];
		$verifiedbyAnesthesiologistName = $ViewOpRoomRecordRow["verifiedbyAnesthesiologistName"];
		$sxPlanReviewedBySurgeon = $ViewOpRoomRecordRow["sxPlanReviewedBySurgeon"];
		$sxPlanReviewedBySurgeonChk = $ViewOpRoomRecordRow["sxPlanReviewedBySurgeonChk"];
		$sxPlanReviewedBySurgeonDateTimeFormat = $objManageData->getFullDtTmFormat($ViewOpRoomRecordRow["sxPlanReviewedBySurgeonDateTime"]);
		
		//$signatureOfNurse = $ViewOpRoomRecordRow["nurseSignOnFile"];
		//$signatureOfSurgeon = $ViewOpRoomRecordRow["surgeonSignOnFile"];
		//$procedureSecondaryVerified = $ViewOpRoomRecordRow["procedureSecondaryVerified"];
		//$signatureOfAnesthesiologist = $ViewOpRoomRecordRow["anesthesiologistSignOnFile"];
		$preOpDiagnosis = $ViewOpRoomRecordRow["preOpDiagnosis"];
		$operativeProcedures = $ViewOpRoomRecordRow["operativeProcedures"];
		$bssValue = $ViewOpRoomRecordRow["bssValue"];
	    $iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
		$iol_ScanUpload2 = $ViewOpRoomRecordRow["iol_ScanUpload2"];
		$infusionBottle = $ViewOpRoomRecordRow["infusionBottle"];
		$infusionBottleOther = $ViewOpRoomRecordRow["infusionBottleOther"];
	    $Healon = $ViewOpRoomRecordRow["Healon"];
		$Occucoat = $ViewOpRoomRecordRow["Occucoat"];
		$Provisc = $ViewOpRoomRecordRow["Provisc"];
		$Miostat = $ViewOpRoomRecordRow["Miostat"];
		$HealonGV = $ViewOpRoomRecordRow["HealonGV"];
		$Discovisc = $ViewOpRoomRecordRow["Discovisc"];
		$AmviscPlus = $ViewOpRoomRecordRow["AmviscPlus"];
		$TrypanBlue = $ViewOpRoomRecordRow["TrypanBlue"];
		$Healon5 = $ViewOpRoomRecordRow["Healon5"];
		$Viscoat = $ViewOpRoomRecordRow["Viscoat"];
		$Miochol = $ViewOpRoomRecordRow["Miochol"];
		$OtherSuppliesUsed = $ViewOpRoomRecordRow["OtherSuppliesUsed"];
		
		$HealonList = $ViewOpRoomRecordRow["HealonList"];
		$OccucoatList = $ViewOpRoomRecordRow["OccucoatList"];
		$ProviscList = $ViewOpRoomRecordRow["ProviscList"];
		$MiostatList = $ViewOpRoomRecordRow["MiostatList"];
		$HealonGVList = $ViewOpRoomRecordRow["HealonGVList"];
		$DiscoviscList = $ViewOpRoomRecordRow["DiscoviscList"];
		$AmviscPlusList = $ViewOpRoomRecordRow["AmviscPlusList"];
		$Healon5List = $ViewOpRoomRecordRow["Healon5List"];
		$ViscoatList = $ViewOpRoomRecordRow["ViscoatList"];
		$MiocholList = $ViewOpRoomRecordRow["MiocholList"];
		$percent_txt = $ViewOpRoomRecordRow["percent_txt"];
		$percent = $ViewOpRoomRecordRow["percent"];
		$XylocaineMPF = $ViewOpRoomRecordRow["XylocaineMPF"];
	    $manufacture = $ViewOpRoomRecordRow["manufacture"];
		$lensBrand = $ViewOpRoomRecordRow["lensBrand"];
		$post2DischargeSummary = $ViewOpRoomRecordRow["post2DischargeSummary"];
		$post2OperativeReport = $ViewOpRoomRecordRow["post2OperativeReport"];
		//$model =explode(",", $ViewOpRoomRecordRow["model"]);
		$model =$ViewOpRoomRecordRow["model"];
		$Diopter =$ViewOpRoomRecordRow["Diopter"];
		$iol_comments =stripslashes($ViewOpRoomRecordRow["iol_comments"]);
		$iolConfirmedSurgeonSignOnFile = $ViewOpRoomRecordRow["iolConfirmedSurgeonSignOnFile"];
	    $Betadine = $ViewOpRoomRecordRow["Betadine"];
		$Saline = $ViewOpRoomRecordRow["Saline"];
		$Alcohol = $ViewOpRoomRecordRow["Alcohol"];
		$Prcnt5Betadinegtts = $ViewOpRoomRecordRow["Prcnt5Betadinegtts"];
		$proparacaine = $ViewOpRoomRecordRow["proparacaine"];
		$tetracaine = $ViewOpRoomRecordRow["tetracaine"];
		$tetravisc = $ViewOpRoomRecordRow["tetravisc"];
		$prepSolutionsOther = $ViewOpRoomRecordRow["prepSolutionsOther"];
		$version_num 			= $ViewOpRoomRecordRow['version_num'];
		
		$surgeryORNumber	= $ViewOpRoomRecordRow["surgeryORNumber"];
		/*
		$surgeryTimeIn		= $ViewOpRoomRecordRow["surgeryTimeIn"];
		$surgeryStartTime = $ViewOpRoomRecordRow["surgeryStartTime"];
		if($surgeryStartTime=="00:00:00" || $surgeryStartTime=="") {
			//$surgeryStartTime = date("h:i A");
		}else {
                $surgeryStartTime = $surgeryStartTime; 		      
		
			list($StartHours,$StartMinutes) = explode(":",$surgeryStartTime);
			if($StartHours>12){
			    $am_pm="PM";
			}
			else{
			  $am_pm="AM";
			}
			if($StartHours>=13){
			  $StartHours = $StartHours-12;
			   if(strlen($StartHours)==1){
			      $StartHours="0".$StartHours;
			   }
			}else
			{
			 //DO nothing
			}
			$surgeryStartTime = $StartHours.":".$StartMinutes." ".$am_pm;
		}
		$surgeryEndTime = $ViewOpRoomRecordRow["surgeryEndTime"];
		if($surgeryEndTime=="00:00:00" ||$surgeryEndTime=="") {
			$surgeryEndTime = "";
		}else {
			$surgeryEndTime = $surgeryEndTime; 		      
			$time_split = explode(":",$surgeryEndTime);
			if($time_split[0]=='24') { //to correct previously saved records
			$surgeryEndTime = "12".":".$time_split[1].":".$time_split[2];
			}
			$surgeryEndTime = $objManageData->getTmFormat($surgeryEndTime);

		}
		$surgeryTimeOut		= $ViewOpRoomRecordRow["surgeryTimeOut"];
		*/
		$pillow_under_knees = $ViewOpRoomRecordRow["pillow_under_knees"];
		$head_rest = $ViewOpRoomRecordRow["head_rest"];
		$safetyBeltApplied = $ViewOpRoomRecordRow["safetyBeltApplied"];
		$other_position = $ViewOpRoomRecordRow["other_position"];
		//$surgeryPatientPosition = $ViewOpRoomRecordRow["surgeryPatientPosition"];
		$surgeryPatientPositionOther = $ViewOpRoomRecordRow["surgeryPatientPositionOther"];
		$anesStartTime = $ViewOpRoomRecordRow["anesStartTime"];
		$iol_serial_number = $ViewOpRoomRecordRow["iol_serial_number"];
		$vitalSignGridStatus = $ViewOpRoomRecordRow["vitalSignGridStatus"];
		//echo $anesStart;
		/*
		if($anesStartTime=="00:00:00" || $anesStartTime=="") {
			$anesStartTime = $anesRow['startTime'];
		}*/
		if($anesStartTime=="00:00:00" || $anesStartTime=="") {
			$anesStartTime = "";
		}
		
		$anesEndTime=$ViewOpRoomRecordRow["anesEndTime"];
		/*
		if($anesEndTime=="00:00:00" || $anesEndTime=="") {
			$anesEndTime = $anesRow['stopTime'];
		}*/
		if($anesStartTime=="00:00:00" || $anesStartTime=="") {
			$anesStartTime = "";
		}
		
		$anesEndTime=$ViewOpRoomRecordRow["anesEndTime"];
		/*
		if($anesEndTime=="00:00:00" || $anesEndTime=="") {
			$anesEndTime = $anesRow['stopTime'];
		}*/
		if($anesStartTime=="00:00:00" || $anesStartTime=="") {
			$anesStartTime = "";
		}
		
//GET PATIENT DETAIL
	$OpRoom_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
	$OpRoom_patientName_tblRes = imw_query($OpRoom_patientName_tblQry) or die(imw_error());
	$OpRoom_patientName_tblRow = imw_fetch_array($OpRoom_patientName_tblRes);
	$OpRoom_patientName = $OpRoom_patientName_tblRow["patient_lname"].",".$OpRoom_patientName_tblRow["patient_fname"]." ".$OpRoom_patientName_tblRow["patient_mname"];

	$OpRoom_patientConfirmDosTemp = $OpRoom_patientConfirm_tblRow["dos"];
	$finalizeStatus = $OpRoom_patientConfirm_tblRow["finalize_status"];

	$OpRoom_patientConfirmDos_split = explode("-",$OpRoom_patientConfirmDosTemp);
	$OpRoom_patientConfirmDos = $OpRoom_patientConfirmDos_split[1]."-".$OpRoom_patientConfirmDos_split[2]."-".$OpRoom_patientConfirmDos_split[0];
	$OpRoom_patientConfirmSurgeon = $OpRoom_patientConfirm_tblRow["surgeon_name"];
	$OpRoom_patientConfirmSiteTemp = $OpRoom_patientConfirm_tblRow["site"];
	// APPLYING NUMBERS TO PATIENT SITE
		if($OpRoom_patientConfirmSiteTemp == 1) {
			$OpRoom_patientConfirmSite = "Left Eye";  //OD
		}else if($OpRoom_patientConfirmSiteTemp == 2) {
			$OpRoom_patientConfirmSite = "Right Eye";  //OS
		}else if($OpRoom_patientConfirmSiteTemp == 3) {
			$OpRoom_patientConfirmSite = "Both Eye";  //OU
		}
	// END APPLYING NUMBERS TO PATIENT SITE
	$OpRoom_patientConfirmPrimProc = $OpRoom_patientConfirm_tblRow["patient_primary_procedure"];
	$OpRoom_patientConfirmSecProc = $OpRoom_patientConfirm_tblRow["patient_secondary_procedure"];
	
	if($OpRoom_patientConfirmSecProc!="N/A")
	 {
	   $OpRoom_patientConfirmSecProcTemp="Yes";
	 }
	 else
	 {
	     $OpRoom_patientConfirmSecProcTemp=" ";
	 }
	 $OpRoom_patientConfirmAnesthesiologistId = $OpRoom_patientConfirm_tblRow["anesthesiologist_id"];
	 $OpRoom_patientConfirmNurseId = $OpRoom_patientConfirm_tblRow["nurseId"];
	 $OpRoom_patientConfirmSurgeonId = $OpRoom_patientConfirm_tblRow["surgeonId"];
	 $OpRoomAnesthesiologistName = $OpRoom_patientConfirm_tblRow["anesthesiologist_name"];
	 $OpRoomNurseName = $OpRoom_patientConfirm_tblRow["confirm_nurse"];
	 $OpRoomSurgeonName = $objManageData->getUserName($OpRoom_patientConfirmSurgeonId,'Surgeon');

	
	
	//END GET ANESTHESIOLOGIST NAME, NURSE NAME, SURGEON NAME 		

		//$detailsmed
		//$detailsallergies
		//$medsnum
if($opRoomFormStatus=='completed' || $opRoomFormStatus=='not completed'){
	$tablePdfPrint='';
	$tablePdfPrint.='<page backbottom="10mm">'.$head_table;
	$tablePdfPrint.="<style>.bdrbtm{vertical-align:middle;}</style>";
	$tablePdfPrint.='
		<table style="width:700px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
         	<tr>
				<td colspan="2" style="width:700px;" class="fheader">Operating Room Record</td>
			</tr>
			<tr>
				<td style="width:350px;" class="bdrbtm bgcolor bold pl5">Allergies</td>
				<td style="width:350px;" class="bdrbtm bgcolor bold pl5">Meds Taken Today</td>
			</tr>';
					
			$tablePdfPrint.='
			<tr>
				<td style="width:350px;vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:200px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Name</td>
							<td style="width:145px;border-right:1px solid #C0C0C0;" class="bdrbtm pl5 bold">Reaction</td>
						</tr>';
						if(count($getAllergiesName)>0){
							foreach($getAllergiesName as $keyOpRec => $allergiesNameOpRec){
								$tablePdfPrint.='
								<tr>
									<td style="width:200px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($allergiesNameOpRec)).'</td>
									<td style="width:145px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($getAllergiesRect[$keyOpRec])).'</td>
								</tr>';
							}				
						}else{
						$tablePdfPrint.='
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
					$tablePdfPrint.='	
					</table>
				</td>';
				$tablePdfPrint.='
				<td style="width:350px;vertical-align:top;">
					<table style="width:350px; font-size:14px;" cellpadding="0" cellspacing="0">
								<tr>
									<td style="width:125px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Name</td>
									<td style="width:100px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Dosage</td>
									<td style="width:100px;font-weight:bold;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">Sig</td>
								</tr>';
						if($medsnum>0){
							while($detailsMeds=@imw_fetch_array($medsRes)){	
							$tablePdfPrint.='
								<tr>
									<td style="width:125px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_name']).'</td>
									<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_desc']).'</td>
									<td style="width:100px;padding:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">'.htmlentities($detailsMeds['prescription_medication_sig']).'</td>
								</tr>';														
							}
						}else {
							for($q=1;$q<=3;$q++) {
								$tablePdfPrint.='
								<tr>
									<td style="width:125px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
									<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
									<td style="width:100px;padding-top:5px;border-left:1px solid #C0C0C0;border-bottom:1px solid #C0C0C0;border-right:1px solid #C0C0C0;">&nbsp;</td>
								</tr>';
							}
						}
				$tablePdfPrint.='		
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="bgcolor bold pl5 bdrbtm">Surgery</td>
			</tr>
			<tr>
				<td colspan="2" style="width:700px;">
					<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:250px;" class="pl5 bold bdrbtm">OR</td>
							<td style="width:250px;" class="pl5 bold bdrbtm">Time In</td>
							<td style="width:205px;" class="pl5 cbold bdrbtm">Time Out</td>
						</tr>
						<tr>
							<td colspan="3" style="width:710px; font-size:13px;" class="bdrbtm"><b>Room:&nbsp;</b>';
							if($surgeryORNumber){
								$tablePdfPrint.=$surgeryORNumber;
							}else{
								$tablePdfPrint.="_____";
							}
							$tablePdfPrint.='&nbsp;&nbsp;<b>In Room Time:&nbsp;</b>';
							if($surgeryTimeIn && $surgeryTimeIn!="00:00 PM" && $surgeryTimeIn!="00:00 AM"){
								$tablePdfPrint.=$surgeryTimeIn;	
							}else{$tablePdfPrint.="_____";}
							$tablePdfPrint.='
							&nbsp;&nbsp;<b>Surgery Start Time:&nbsp;</b>';
							if($surgeryStartTime && $surgeryStartTime!="00:00 PM" && $surgeryStartTime!="00:00 AM" && $surgeryStartTime!="00:00:00"){
								$tablePdfPrint.=$surgeryStartTime;	
							}else{$tablePdfPrint.="_____";}
							$tablePdfPrint.='&nbsp;&nbsp;<b>Surgery End Time:&nbsp;</b>';
							if($surgeryEndTime && $surgeryEndTime!="00:00 PM" && $surgeryEndTime!="00:00 AM"){
								$tablePdfPrint.=$surgeryEndTime;
							}else{$tablePdfPrint.="_____";}
							$tablePdfPrint.='&nbsp;&nbsp;<b>Out of Room:&nbsp;</b>';
							if($surgeryTimeOut && $surgeryTimeOut!="00:00 PM" && $surgeryTimeOut!="00:00 AM"){
								$tablePdfPrint.=$surgeryTimeOut;
							}else{$tablePdfPrint.="_____";}
							$tablePdfPrint.='
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="width:350px;" class="bdrbtm bold bgcolor pl5">Time Out</td>
				<td style="width:350px;" class="bdrbtm cbold bgcolor">Done</td>
			</tr>
			<tr>
				<td style="width:370px;">
					<table style="width:350px;border-right:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:185px;" class="bdrbtm pl5">Patient Identification Verified:</td>
							<td style="width:170px;" class="bold bdrbtm">';
							if($OpRoom_patientName){
								$tablePdfPrint.=$OpRoom_patientName;
							}else{$tablePdfPrint.="___________";}
							$tablePdfPrint.='
							</td>
						</tr>
						<tr>
							<td style="width:185px;" class="bdrbtm pl5">Site Verified:</td>
							<td style="width:170px;" class="bold bdrbtm">';
							if($OpRoom_patientConfirmSite){
								$tablePdfPrint.=$OpRoom_patientConfirmSite;
							}else{$tablePdfPrint.="___________";}
							$tablePdfPrint.='
							</td>
						</tr>
						<tr>
							<td style="width:185px;vertical-align:top;" class="bdrbtm pl5">Procedure Verified:</td>
							<td style="width:170px;" class="bold bdrbtm">';
							if($OpRoom_patientConfirmPrimProc){
								$tablePdfPrint.=$OpRoom_patientConfirmPrimProc;
							}else{$tablePdfPrint.="___________";}
							$tablePdfPrint.='
							</td>
						</tr>
						<tr>
							<td style="width:185px;vertical-align:top;" class="bdrbtm pl5">Secondary Verified:</td>
							<td style="width:170px;" class="bold bdrbtm">';
							if($OpRoom_patientConfirmSecProc){
								$tablePdfPrint.=$OpRoom_patientConfirmSecProc;
							}else{$tablePdfPrint.="___________";}
							$tablePdfPrint.='
							</td>
						</tr>
					</table>
				</td>
				<td style="width:350px;vertical-align:top;">
					<table style="width:350px;border-left:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:150px;" class="bdrbtm pl5">Nurse:</td>
							<td style="width:200px;" class="bold bdrbtm">';
							$statusnurse = $verifiedbySurgeonstatus = $verifiedbyAnesthesiologiststatus = $sxPlanReviewedBySurgeonstatus = "";
							if($verifiedbyNurse=="Yes"){
							   $statusnurse="Done";
							}	
							if($verifiedbySurgeon=="Yes"){
							   $verifiedbySurgeonstatus="Done";
							}  
							if($verifiedbyAnesthesiologist=="Yes" ){
							   $verifiedbyAnesthesiologiststatus="Done";
							}
							if($sxPlanReviewedBySurgeon=="Yes"){
							   $sxPlanReviewedBySurgeonstatus="Done";
							}
							if($verifiedbyNurse=="Yes" && $verifiedbyNurseName){
								$tablePdfPrint.=stripslashes($verifiedbyNurseName);
							}else{$tablePdfPrint.="____";}
							if($statusnurse){
								$tablePdfPrint.="&nbsp;&nbsp;&nbsp;<b>".$statusnurse."</b>";
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.='
							</td>
						</tr>
						<tr>
							<td style="width:150px;" class="bdrbtm pl5">Surgeon:</td>
							<td style="width:200px;" class="bold bdrbtm">';
							if($OpRoomSurgeonName){
								$tablePdfPrint.=stripslashes($OpRoomSurgeonName);
							}else{$tablePdfPrint.="____";}
							if($verifiedbySurgeonstatus){
								$tablePdfPrint.="&nbsp;&nbsp;&nbsp;<b>".$verifiedbySurgeonstatus."</b>";
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.='
							</td>
						</tr>
						<tr>
							<td style="width:150px;" class="bdrbtm pl5">Anesthesia Provider:</td>
							<td style="width:200px;" class="bold bdrbtm">';
							if($OpRoomAnesthesiologistName){
								$tablePdfPrint.=stripslashes($OpRoomAnesthesiologistName);
							}else{$tablePdfPrint.="____";}
							if($verifiedbyAnesthesiologiststatus){
								$tablePdfPrint.="&nbsp;&nbsp;&nbsp;<b>".$verifiedbyAnesthesiologiststatus."</b>";
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.='
							</td>
						</tr>
						<tr>
							<td style="width:150px;" class="bdrbtm pl5">Time:</td>
							<td style="width:200px;" class="bold bdrbtm">';
							if($verifiedbyNurse && $verifiedbyNurseTime){
								$tablePdfPrint.=$verifiedbyNurseTime;
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.='
							</td>
						</tr>';
						if($version_num > 1 && $sxPlanReviewedBySurgeonChk == "1") {
							$tablePdfPrint.='
						<tr>
							<td style="width:150px;" class="bdrbtm pl5">Sx Plan Sheet Reviewed:<br>(By Surgeon)</td>
							<td style="width:200px;" class="bold bdrbtp">';
							if($OpRoomSurgeonName){
								$tablePdfPrint.=stripslashes($OpRoomSurgeonName);
							}else{$tablePdfPrint.="____";}
							if($sxPlanReviewedBySurgeonstatus){
								$tablePdfPrint.="&nbsp;&nbsp;&nbsp;<b>".$sxPlanReviewedBySurgeonstatus."<br>(".$sxPlanReviewedBySurgeonDateTimeFormat.")</b>";
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.='
							</td>
						</tr>';
						}
						$tablePdfPrint.='
					</table>			
				</td>
			</tr>
			<tr>
				<td colspan="2" style="width:700px;" class="bdrtop">
					<table style="width:700px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:130px;vertical-align:top;" class="bold bdrbtm pl5">Pre-Op Diagnosis:</td>
							<td style="width:220px;vertical-align:top;" class="bdrbtm">';
							if(trim($preOpDiagnosis)){
								$tablePdfPrint.=htmlentities(stripslashes($preOpDiagnosis));	
							}
							$tablePdfPrint.='
							</td>
							<td style="width:150px;vertical-align:top;" class="bold bdrbtm pl5">Operative Procedures:</td>
							<td style="width:200px;" class="bdrbtm">';
							if(trim($operativeProcedures)){
								$tablePdfPrint.=htmlentities(stripslashes($operativeProcedures));	
							}
							$tablePdfPrint.='
							</td>
						</tr>
						<tr>
							<td style="width:130px;vertical-align:top;" class="bold bdrbtm pl5">Post-Op Diagnosis:</td>
							<td style="width:220px;vertical-align:top;" class="bdrbtm">';
							if(trim($postOpDiagnosis)){
								$tablePdfPrint.=htmlentities(stripslashes($postOpDiagnosis));	
							}
							$tablePdfPrint.='
							</td>';
							if(constant("DISABLE_OPROOM_POSTOP_MED")=="YES") {
								$tablePdfPrint .= '<td colspan="2" style="width:350px;vertical-align:top;">&nbsp;</td>';	
							}else {
								$tablePdfPrint.='
								<td style="width:150px;vertical-align:top;" class="bold bdrbtm pl5">Post-Op Orders:</td>
								<td style="width:200px;" class="bdrbtm">';
								if(trim($postOpDrops)){
									$tablePdfPrint.=htmlentities(stripslashes($postOpDrops));	
								}
								$tablePdfPrint.='
								</td>';
							}
							$tablePdfPrint.='
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="width:350px; vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="2" style="width:350px;" class="pl5 bdrbtm bold bgcolor">Product Control</td>
						</tr>
						<tr>
							<td colspan="2" style="width:350px;" class="bdrbtm bold pl5">';
							if($bssValue){
								if(strtolower($bssValue)=="bssplus"){
									$tablePdfPrint.="BSS Plus: Yes";
								}else{
									$tablePdfPrint.=strtoupper($bssValue).": Yes";
								}
							}else{$tablePdfPrint.="_____";}
							$tablePdfPrint.='
							</td>							
						</tr>
						<tr>
							<td style="width:160px;" class="bdrbtm">Added To Infusion Bottle</td>
							<td style="width:200px;font-size:13px;line-height:1.5;border-right:1px solid #C0C0C0;" class="bdrbtm">
							Epinephrine 0.3ml (300mcg) ';
							if($Epinephrine03){
								$tablePdfPrint.="<b>".$Epinephrine03."</b>";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.="<br>Vancomycin 0.1 ml (510mg)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							if($Vancomycin01){
								$tablePdfPrint.="<b>".$Vancomycin01."</b>";	
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.="<br>Vancomycin 0.2 ml (10mg)&nbsp;&nbsp;&nbsp;";
							if($Vancomycin02){
								$tablePdfPrint.="<b>".$Vancomycin02."</b>";	
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.="<br>Omidria&nbsp;&nbsp;&nbsp;";
							if($omidria){
								$tablePdfPrint.="<b>".$omidria."</b>";	
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.="&nbsp;Other:&nbsp;";
							if($InfusionOtherChk=="Yes" && $infusionBottleOther!=""){
								$tablePdfPrint.=$infusionBottleOther;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							
							</td>							
						</tr>
					</table>
				</td>
				<td style="width:350px; vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="2" style="width:350px;" class="pl5 bdrbtm bold bgcolor">IOL</td>
						</tr>
						<tr>
							<td style="width:100px;" class="bold bdrbtm">Scan/Upload:</td>
							<td style="width:250px;text-align:left;" class="bdrbtm">IOL<b>&nbsp;S/N:&nbsp;</b>';
							if($iol_serial_number){$tablePdfPrint.=$iol_serial_number;}else{$tablePdfPrint.='___________________';}
							$tablePdfPrint.='</td>
						</tr>
						<tr>
							<td style="width:100px;border:1px solid #C0C0C0; text-align:center;"  class="bdrbtm">';
								if($iol_ScanUpload!=''){
									$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload);
									imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom.jpg');
									if(file_exists("html2pdfnew/oproom.jpg")){
										$tablePdfPrint.='<img src="../html2pdfnew/oproom.jpg" style="width:100px; height:100px;">';
									}
								}
							$tablePdfPrint.='
							</td>		
							<td style="width:250px;vertical-align:top;" class="bdrbtm">';
								if($iol_ScanUpload2!=''){
									$bakImgResourceOproom2 = imagecreatefromstring($iol_ScanUpload2);
									imagejpeg($bakImgResourceOproom2,'html2pdfnew/oproom2.jpg');
									if(file_exists("html2pdfnew/oproom2.jpg")){
										$tablePdfPrint.='<img src="../html2pdfnew/oproom2.jpg" style="width:100px; height:100px;">';
									}
								}
								/*if($post2DischargeSummary){
									$tablePdfPrint.="<b>".$post2DischargeSummary."</b>";		
								}else{$tablePdfPrint.="__";}
								$tablePdfPrint.='<br>Post to Operative Report:&nbsp;';
								if($post2OperativeReport){
									$tablePdfPrint.="<b>".$post2OperativeReport."</b>";		
								}else{$tablePdfPrint.="__";}*/
							$tablePdfPrint.='	
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="width:350px;vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:350px;" class="bdrbtm bold bgcolor pl5">Supplies Used</td>
						</tr>
						<tr>
							<td style="width:350px;">
								<table style="width:350px;border-right:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">';
									
									$condArray	=	array(); 
									$condArray['confirmation_id']	=	$pConfId ;
									$condArray['displayStatus']		=	1 ;
									$suppliesUsed	=	$objManageData->getMultiChkArrayRecords('operatingroomrecords_supplies',$condArray,'suppName','Asc');
									
									if( is_array($suppliesUsed) && count($suppliesUsed) > 0 )
									{
										$suppliesCounter = 0;	
										$tablePdfPrint.='<tr>';
										foreach($suppliesUsed as $supply)
										{	
											$suppliesCounter++;
											
											
											$tablePdfPrint.='<td style="width:80px;" class="bdrbtm">'.htmlentities(stripslashes($supply->suppName)).':&nbsp;</td>';
											$tablePdfPrint.='<td style="width:30px;" class="bdrbtm">';
											if($supply->suppQtyDisplay && $supply->suppChkStatus && $supply->suppList !='' )
											{
												$tablePdfPrint.="<b>".$supply->suppList."</b>";
											}
											elseif ( !$supply->suppQtyDisplay && $supply->suppChkStatus  )
											{
												$tablePdfPrint.="<b>Yes</b>";	
											}
											else{$tablePdfPrint.="___";}
											$tablePdfPrint.='</td>';
												
											if($suppliesCounter%3 == 0 ) { $tablePdfPrint.='</tr><tr>'; } 
											
										}
										$tablePdfPrint.='</tr>';
									}
									
									/*
									<tr>
										<td style="width:80px;" class="bdrbtm">Healon:&nbsp;</td>
										<td style="width:30px;" class="bdrbtm">';
										if($Healon=="Yes" || $HealonList!=''){
											$tablePdfPrint.="<b>".$HealonList."</b>";
										}
										$tablePdfPrint.='
										</td>
										<td style="width:90px;" class="bdrbtm">HealonGV:&nbsp;</td>
										<td style="width:30px;" class="bdrbtm">';
										if($HealonGV=="Yes" || $HealonGVList!=''){
											$tablePdfPrint.="<b>".$HealonGVList."</b>";
										}else{$tablePdfPrint.="___";}
										$tablePdfPrint.='
										</td>
										<td style="width:80px;" class="bdrbtm">Healon5:&nbsp;</td>
										<td style="width:35px;" class="bdrbtm">';
										if($Healon5 == "Yes" || $Healon5List!=''){
											$tablePdfPrint.="<b>".$Healon5List."</b>";
										}
										$tablePdfPrint.='</td>
									</tr>
									<tr>
										<td style="width:80px;" class="bdrbtm">Occucoat:&nbsp;</td>
										<td style="width:30px;" class="bdrbtm">';
										if($Occucoat== "Yes" || $OccucoatList!=''){
											$tablePdfPrint.="<b>".$OccucoatList."</b>";
										}
										$tablePdfPrint.='
										</td>
										<td style="width:90px;" class="bdrbtm">Duovisc:&nbsp;</td>
										<td style="width:30px;" class="bdrbtm">';
										if($Discovisc == "Yes" || $DiscoviscList!=''){
											$tablePdfPrint.="<b>".$DiscoviscList."</b>";
										}else{$tablePdfPrint.="___";}
										$tablePdfPrint.='
										</td>
										<td style="width:80px;" class="bdrbtm">Viscoat:&nbsp;</td>
										<td style="width:35px;" class="bdrbtm">';
										if($Viscoat== "Yes" || $ViscoatList!=''){
											$tablePdfPrint.="<b>".$ViscoatList."</b>";
										}
										$tablePdfPrint.='</td>
									</tr>
									<tr>
										<td style="width:80px;" class="bdrbtm">Provisc:&nbsp;</td>
										<td style="width:30px;" class="bdrbtm">';
										if($Provisc == "Yes" || $ProviscList!=''){
											$tablePdfPrint.="<b>".$ProviscList."</b>";
										}
										$tablePdfPrint.='
										</td>
										<td style="width:90px;" class="bdrbtm">Amvisc Plus:&nbsp;</td>
										<td style="width:30px;" class="bdrbtm">';
										if($AmviscPlus == "Yes" || $AmviscPlusList!=''){
											$tablePdfPrint.="<b>".$AmviscPlusList."</b>";
										}else{$tablePdfPrint.="___";}
										$tablePdfPrint.='
										</td>
										<td style="width:80px;" class="bdrbtm">Miochol:&nbsp;</td>
										<td style="width:35px;" class="bdrbtm">';
										if($Miochol == "Yes" || $MiocholList!=''){
											$tablePdfPrint.="<b>".$MiocholList."</b>";
										}
										$tablePdfPrint.='</td>
									</tr>
									<tr>
										<td style="width:80px;" class="bdrbtm">Provisc:&nbsp;</td>
										<td style="width:30px;" class="bdrbtm">';
										if($Provisc == "Yes" || $ProviscList!=''){
											$tablePdfPrint.="<b>".$ProviscList."</b>";
										}
										$tablePdfPrint.='
										</td>
										<td style="width:90px;" class="bdrbtm">Amvisc Plus:&nbsp;</td>
										<td style="width:30px;" class="bdrbtm">';
										if($AmviscPlus == "Yes" || $AmviscPlusList!=''){
											$tablePdfPrint.="<b>".$AmviscPlusList."</b>";
										}else{$tablePdfPrint.="___";}
										$tablePdfPrint.='
										</td>
										<td style="width:80px;" class="bdrbtm">Miochol:&nbsp;</td>
										<td style="width:35px;" class="bdrbtm">';
										if($Miochol == "Yes" || $MiocholList!=''){
											$tablePdfPrint.="<b>".$MiocholList."</b>";
										}
										$tablePdfPrint.='</td>
									</tr>
									<tr>
										<td style="width:80px;" class="bdrbtm">Miostat:&nbsp;</td>
										<td style="width:30px;" class="bdrbtm">';
										if($Miostat== "Yes" || $MiostatList!=''){
											$tablePdfPrint.="<b>".$MiostatList."</b>";
										}
										$tablePdfPrint.='
										</td>
										<td style="width:90px;" class="bdrbtm">Trypan Blue:&nbsp;</td>
										<td style="width:30px;" class="bdrbtm">';
										if($TrypanBlue=="Yes"){
											$tablePdfPrint.="<b>".$TrypanBlue."</b>";
										}else{$tablePdfPrint.="___";}
										$tablePdfPrint.='
										</td>
										<td style="width:80px;" class="bdrbtm">Xylocaine MPF 1%:&nbsp;</td>
										<td style="width:35px;" class="bdrbtm">';
										if($XylocaineMPF== "Yes"){
											$tablePdfPrint.="<b>".$XylocaineMPF."</b>";
										}else{$tablePdfPrint.="___";}
										$tablePdfPrint.='
										</td>
									</tr>
									
									
									*/
									if(trim($OtherSuppliesUsed))
									{
										$tablePdfPrint.='<tr>
											<td style="width:80px;">Other:</td>
											<td colspan="5" style="width:265px;">';
											if(trim($OtherSuppliesUsed)){
												$tablePdfPrint.=trim($OtherSuppliesUsed);
											}else{
												$tablePdfPrint.="____";
											}
											$tablePdfPrint.='
											</td>
										</tr>';
									}
								$tablePdfPrint.='</table>
							</td>
						</tr>
					</table>
				</td>
				<td style="width:350px;vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:350px;" class="bgcolor bdrbtm bold pl5">IOL Manufacturer</td>
						</tr>
						<tr>
							<td style="width:350px;">
								<table style="width:350px;" cellpadding="0" cellspacing="0">
									<tr>
										<td style="width:50px;" class="bold bdrbtm">Man:</td>
										<td style="width:100px;" class="bdrbtm">';
										if(trim($manufacture)){
											$tablePdfPrint.=$manufacture;
										}else{$tablePdfPrint.="____";}
										$tablePdfPrint.='
										</td>
										<td style="width:90px;" class="bold bdrbtm">Lens Brand:</td>
										<td style="width:110px;" class="bdrbtm">';
										if(trim($lensBrand)){
											$tablePdfPrint.=$lensBrand;
										}else{$tablePdfPrint.="____";}
										$tablePdfPrint.='
										</td>
									</tr>
									<tr>
										<td style="width:50px;vertical-align:top;" class="bold bdrbtm">Model:</td>
										<td style="width:100px;" class="bdrbtm">';
										if(trim($model)){
											$tablePdfPrint.=$model;
										}else{$tablePdfPrint.="____";}
										$tablePdfPrint.='
										</td>
										<td style="width:90px;" class="bold bdrbtm">Diopter:</td>
										<td style="width:110px;" class="bdrbtm">';
										if(trim($Diopter)){
											$tablePdfPrint.=$Diopter;
										}else{$tablePdfPrint.="____";}
										$tablePdfPrint.='
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="width:350px; vertical-align:top;" class="bdrbtm"><b>IOL Comments: </b>';
							if(trim($iol_comments)){
								$tablePdfPrint.= $iol_comments;
							}else{$tablePdfPrint.="________";}
							$tablePdfPrint.='
							</td>
						</tr>
						
						<tr>
							<td colspan="2" style="width:350px;" class="bgcolor bdrbtm bold pl5">IOL and/or Consent Confirmed</td>
						</tr>
						<tr>
							<td colspan="2" style="width:350px;" class="bdrbtm">';
							if($signNurseStatus=="Yes"){
								$tablePdfPrint.="<b>Nurse:&nbsp;</b>".$signNurseName;
								$tablePdfPrint.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$tablePdfPrint.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($ViewOpRoomRecordRow["signNurseDateTime"]);
							}else {
								$tablePdfPrint.="<b>Nurse:&nbsp;</b>________";
								$tablePdfPrint.="<br><b>Electronically Signed:&nbsp;</b>________";
								$tablePdfPrint.="<br><b>Signature Date:&nbsp;</b>________";
							}
							$tablePdfPrint.='	
							</td>
						</tr>
						<tr>
							<td colspan="2" style="width:350px;"><b>Prep Solutions:</b>&nbsp;Betadine 10%:&nbsp;';
							if($Betadine){
								$tablePdfPrint.="<b>".$Betadine."</b>";
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.="&nbsp;&nbsp;<b>Saline:&nbsp;</b>";
							if($Saline){
								$tablePdfPrint.=$Saline;
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>
							<td colspan="2" style="width:350px">Alcohol:&nbsp;';
							if($Alcohol){
								$tablePdfPrint.=$Alcohol;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='&nbsp;&nbsp;5% Betadine gtts:&nbsp;';
							if($Prcnt5Betadinegtts != ""){
								$tablePdfPrint.="<b>".$Prcnt5Betadinegtts."</b>";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.="&nbsp;Proparacaine:&nbsp;";
							if($proparacaine!=""){
								$tablePdfPrint.="<b>".$proparacaine."</b>";
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>
							<td colspan="2" style="width:350px">Tetracaine:&nbsp;';
							if($tetracaine){
								$tablePdfPrint.="<b>".$tetracaine."</b>";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='&nbsp;&nbsp;Tetravisc:&nbsp;';
							if($tetravisc != ""){
								$tablePdfPrint.="<b>".$tetravisc."</b>";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.="&nbsp;Other:&nbsp;";
							if($prepSolutionsOther!=""){
								$tablePdfPrint.=$prepSolutionsOther;
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.=
							'</td>
						</tr>	
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="width:700px;" class="bgcolor bold bdrbtm">Patient Position</td>
			</tr>
			<tr>
				<td colspan="2" style="width:700px;" class="bdrbtm"><b>Pillow Under Knees:&nbsp;</b>';
				 if($pillow_under_knees!=''){
					 $tablePdfPrint.=$pillow_under_knees;
				 }else{$tablePdfPrint.="___";}
				$tablePdfPrint.='&nbsp;&nbsp;<b>Head Rest:</b>&nbsp;';
				if($head_rest!=''){
					$tablePdfPrint.=$head_rest;
				}else{$tablePdfPrint.="___";}
					$tablePdfPrint.='&nbsp;&nbsp;<b>Safety Belt Applied:&nbsp;</b>';
				if($safetyBeltApplied!=''){
					$tablePdfPrint.=$safetyBeltApplied;
				}else{$tablePdfPrint.="___";}
				$tablePdfPrint.='&nbsp;&nbsp;&nbsp;<b>Other</b>&nbsp;';
				if($surgeryPatientPositionOther!=''){
					$tablePdfPrint.=$surgeryPatientPositionOther;
				}else{$tablePdfPrint.="___";}
				$tablePdfPrint.=
				'</td>
			</tr>
			<tr>
				<td class="bdrbtm bold bgcolor">Intra Op Inj</td>
				<td class="bdrbtm bold bgcolor">Anesthesia Service</td>
			</tr>
			<tr>
				<td style="width:350px;">
					<table style="width:350px;border-right:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:85px;" class="bdrbtm">Solumedrol:&nbsp;</td>
							<td style="width:15px;" class="cbold bdrbtm">';
							if($Solumedrol){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:60px;" class="bdrbtm">';
							if($SolumedrolList){
								$tablePdfPrint.=$SolumedrolList;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
							<td style="width:75px;" class="bdrbtm">Ancef:&nbsp;</td>
							<td style="width:15px;" class="bdrbtm cbold">';
							if($Ancef){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
							<td style="width:70px;" class="bdrbtm">';
							if($AncefList){
								$tablePdfPrint.=$AncefList;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>
							<td style="width:75px;" class="bdrbtm">Dexamethasone:</td>
							<td style="width:15px;" class="cbold bdrbtm">';
							if($Dexamethasone){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:60px;" class="bdrbtm">';
							if($DexamethasoneList){
								$tablePdfPrint.=$DexamethasoneList;	
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:75px;" class="bdrbtm">Gentamicin:&nbsp;</td>
							<td style="width:15px;" class="bdrbtm cbold">';
							if($Gentamicin){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
							<td style="width:70px;" class="bdrbtm">';
							if($GentamicinList){
								$tablePdfPrint.=$GentamicinList;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>
							<td style="width:75px;" class="bdrbtm">Kenalog:</td>
							<td style="width:15px;" class="cbold bdrbtm">';
							if($Kenalog){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:60px;" class="bdrbtm">';
							if($KenalogList){
								$tablePdfPrint.=$KenalogList;	
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:75px;" class="bdrbtm">Depomedrol:&nbsp;</td>
							<td style="width:15px;" class="bdrbtm cbold">';
							if($Depomedrol){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
							<td style="width:70px;" class="bdrbtm">';
							if($DepomedrolList){
								$tablePdfPrint.=$DepomedrolList;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>
							<td style="width:75px;" class="bdrbtm">Vancomycin:</td>
							<td style="width:15px;" class="cbold bdrbtm">';
							if($Vancomycin){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:60px;" class="bdrbtm">';
							if($VancomycinList){
								$tablePdfPrint.=$VancomycinList;	
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:75px;" class="bdrbtm">Trimoxi:&nbsp;</td>
							<td style="width:15px;" class="bdrbtm cbold">';
							if($Trimaxi){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
							<td style="width:70px;" class="bdrbtm">';
							if($TrimaxiList){
								$tablePdfPrint.=$TrimaxiList;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>	
							<td style="width:75px;" class="bdrbtm">XylocaineMPF:&nbsp;</td>
							<td style="width:15px;" class="bdrbtm cbold">';
								$tablePdfPrint.=($injXylocaineMPF ? "Y" : "___").
							'</td>
							<td style="width:60px;" class="bdrbtm">';
								$tablePdfPrint.=($injXylocaineMPFList ? $injXylocaineMPFList : "___").
							'</td>
							<td style="width:75px;" class="bdrbtm">Miostat:&nbsp;</td>
							<td style="width:15px;" class="bdrbtm cbold">';
								$tablePdfPrint.=($injMiostat ? "Y" : "___").
							'</td>
							<td style="width:70px;" class="bdrbtm">';
								$tablePdfPrint.=($injMiostatList ? $injMiostatList : "___").
							'</td>
						</tr>
						<tr>
							<td style="width:75px;" class="bdrbtm">Phenyl/Lido 1.5%/1%:</td>
							<td style="width:15px;" class="cbold bdrbtm">';
							if($PhenylLido){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:60px;" class="bdrbtm">';
							if($PhenylLidoList){
								$tablePdfPrint.=$PhenylLidoList;	
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td  colspan="3" style="width:150px;" class="bdrbtm">Other:&nbsp;';
							if($postOpInjOther){
								$tablePdfPrint.=$postOpInjOther;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>
							<td colspan="6" style="width:350px;" class="bdrbtm">Patch:&nbsp;';
							 if(trim($patch)){
								$tablePdfPrint.="<b>".trim($patch)."</b>";	 
								 }else{$tablePdfPrint.="___";}
							$tablePdfPrint.='&nbsp;Shield&nbsp;';
							if($shield){
								$tablePdfPrint.="<b>Y</b>";
							}else{$tablePdfPrint.="__";}
							$tablePdfPrint.='&nbsp;Needle/Suture count&nbsp;&nbsp; Correct';
							if($needleSutureCount){
								$tablePdfPrint.="&nbsp;<b>".$needleSutureCount."</b>";
							}else{$tablePdfPrint.="__";}
							$tablePdfPrint.='
							</td>
						</tr>';
						if( $version_num > 4){
							$tablePdfPrint.='
						<tr>
							<td style="width:100px;" class="bdrbtm bold">Complications:</td>
							<td colspan="5" style="width:240px;" class="bdrbtm">';
							if(stripslashes($complications)){
								$tablePdfPrint.=htmlentities(stripslashes($complications));	
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.='
							</td>
						</tr>';
						}
						$tablePdfPrint.='
						<tr>
							<td style="width:100px;" class="bdrbtm bold">Post Op Orders:</td>
							<td colspan="5" style="width:240px;" class="bdrbtm">';
							if(stripslashes($intraOpPostOpOrders)){
								$tablePdfPrint.=stripslashes($intraOpPostOpOrders);	
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.='
							</td>
						</tr>
						<tr>
							<td style="width:100px;" class="bdrbtm bold">Nurse Notes:</td>
							<td colspan="5" style="width:240px;" class="bdrbtm">';
							if(stripslashes($nurseNotes)){
								$tablePdfPrint.=htmlentities(stripslashes($nurseNotes));	
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.='
							</td>
						</tr>';
						if( $version_num > 2){
							$tablePdfPrint.='
						<tr>
							<td style="width:100px;" class="bdrbtm bold">Others Present:</td>
							<td colspan="5" style="width:240px;" class="bdrbtm">';
							if(stripslashes($others_present)){
								$tablePdfPrint.=htmlentities(stripslashes($others_present));	
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.='
							</td>
						</tr>';
						}
						$tablePdfPrint.='
					</table>
				</td>			
				<td style="width:350px;vertical-align:top;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:350px;">Anesthesia service provided:&nbsp;';
							if(trim($anesthesia_service)=="full_anesthesia"){
								$tablePdfPrint.="<b>Full</b>";	
							}else if(trim($anesthesia_service)=="no_anesthesia"){
								$tablePdfPrint.="<b>No</b>";		
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='</td>
						</tr>
						<tr>
							<td style="width:350px;" class="bdrbtm pl5">';
							if(trim($TopicalBlock)!=""){
								$tablePdfPrint.=$TopicalBlock.":&nbsp;<b>Y</b>";	
							}else{$tablePdfPrint.="Block:&nbsp;___&nbsp;&nbsp;Local:&nbsp;___&nbsp;&nbsp;Topical:___";}
							
							$tablePdfPrint.='
							</td>
						</tr>
						<tr style="width:350px;" class="bdrbtm pl5">	
							<td>
								 <table style="width:350px;" cellpadding="0" cellspacing="0">';
								 $tmp_colspan= ($collagenShield=='Yes') ? '3' : '1';
								 if($collagenShield=='Yes'){
								 	$tablePdfPrint.='
									<tr>
										<td style="width:150px;" class="bdrbtm">Collagen Shield:&nbsp;';//this field is depriciated. show only in case of saved one
										if($collagenShield){
											$tablePdfPrint.="<b>".$collagenShield."</b>";	
										}else{$tablePdfPrint.="___";}
										$tablePdfPrint.='</td>
										<td style="width:100px;" class="bdrbtm">Soaked in</td>
										<td style="width:100px; text-align:left;line-height:1.5;" class="bdrbtm">
										Econopred:&nbsp;&nbsp;';
										if($Econopred!=""){
											$tablePdfPrint.="<b>Y</b>";
										}else{$tablePdfPrint.="___";}
										$tablePdfPrint.=
										'<br>Zymar:&nbsp;&nbsp;';
										if($Zymar!=""){
											$tablePdfPrint.="<b>Y</b>";
										}else{$tablePdfPrint.="___";}
										
										$tablePdfPrint.='<br>Tobradax:&nbsp;&nbsp;';
										if($Tobradax != ""){
											$tablePdfPrint.="<b>Y</b>";
										}
										
										$tablePdfPrint.='Other:&nbsp;';
										if($soakedInOtherChk != "" && $soakedInOther!=""){
											$tablePdfPrint.=$soakedInOther;
										}else{$tablePdfPrint.="";}
										$tablePdfPrint.=
										'</td>
									</tr>';
								 }
								 $tablePdfPrint.='	
									<tr>
										<td colspan="'.$tmp_colspan.'" style="width:350px;">
										<b>Comments:</b>&nbsp;';
										if($other_remain!=""){
											$tablePdfPrint.=$other_remain;
										}else{$tablePdfPrint.="____";}
										$tablePdfPrint.=
										'</td>
									</tr>
								</table>
							</td>
						</tr>	
					</table>
				</td>
			</tr>';
			// Vital Sign Grid Printing Section - Start
			if($vitalSignGridStatus)
			{
					$tablePdfPrint.='<tr>';
					$tablePdfPrint.='<td style="width:700px;" class="bdrbtm" colspan="2" valign="top"><table style="width:700px; " cellpadding="0" cellspacing="0">';
					
					$tablePdfPrint.='<tr>';
					$tablePdfPrint.='<td class="bgcolor bold bdrbtm" colspan="8" style="width:700px; vertical-align:middle; ">&nbsp;&nbsp;Vital Signs</td>';
					$tablePdfPrint.='</tr>';
					
					
					$tablePdfPrint.='<tr>';	
					$tablePdfPrint.='<td rowspan="2" class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Time</b></td>';
					$tablePdfPrint.='<td colspan="2" class="bdrbtm" style="width:200px; border-right:1px solid #C0C0C0; vertical-align:middle;">&nbsp;<b>B/P</b></td>';
					$tablePdfPrint.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Pulse</b></td>';
					$tablePdfPrint.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>RR</b></td>';
					$tablePdfPrint.='<td rowspan="2" class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Temp<sup>O</sup> C</b></td>';
					$tablePdfPrint.='<td rowspan="2" class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>EtCO<sub>2</sub></b></td>';
					$tablePdfPrint.='<td rowspan="2" class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;<b>OSat<sub>2</sub></b></td>';		
					$tablePdfPrint.='</tr>';
					$tablePdfPrint.='<tr>';	
					
					$tablePdfPrint.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Systolic</b></td>';
					$tablePdfPrint.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;<b>Diastolic</b></td>';
					
					$tablePdfPrint.='</tr>';
						
					
					
					$condArr		=	array();
					$condArr['confirmation_id']	=	$pConfId ;
					$condArr['chartName']				=	'intra_op_record_form' ;
					
					$gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time','Asc');
		
					$gCounter	=	0;
					if(is_array($gridData) && count($gridData) > 0  )
					{
						foreach($gridData as $gridRow)
						{		
							$gCounter++;
							$fieldValue1= $objManageData->getTmFormat($gridRow->start_time);
							$fieldValue2= $gridRow->systolic;
							$fieldValue3= $gridRow->diastolic;
							$fieldValue4= $gridRow->pulse;
							$fieldValue5= $gridRow->rr;
							$fieldValue6= $gridRow->temp;
							$fieldValue7= $gridRow->etco2;
							$fieldValue8= $gridRow->osat2;
							
							$tablePdfPrint.='<tr>';	
							$tablePdfPrint.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue1.'</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue2.'</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue3.'</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue4.'</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue5.'</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue6.'</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;'.$fieldValue7.'</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;'.$fieldValue8.'</td>';		
							$tablePdfPrint.='</tr>';
								
						}
					}
					
					for($loop = $gCounter; $loop < 3; $loop++)
					{
							$tablePdfPrint.='<tr>';	
							$tablePdfPrint.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:100px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:75px; border-right:1px solid #C0C0C0; vertical-align:middle; ">&nbsp;</td>';
							$tablePdfPrint.='<td class="bdrbtm" style="width:90px; vertical-align:middle; ">&nbsp;</td>';		
							$tablePdfPrint.='</tr>';
					}
					$tablePdfPrint.='</table></td>';
					$tablePdfPrint.='</tr>';
								
			}
			// Vital Sign Grid Printing Section - End
			
			$tablePdfPrint.=
			'<tr>
				<td colspan="2" class="bdrbtm cbold bgcolor">Electronically Signed</td>
			</tr>';
			$Scrub1name="___";
			if($scrubTechId1 !=""){
				$qrscrub1="select lname,fname from users where usersId=$scrubTechId1";
				$qrresscrub1=imw_query($qrscrub1);
				$recordscrub1=imw_fetch_array($qrresscrub1);
				$Scrub1name=$recordscrub1['lname'].','.$recordscrub1['fname'];
			}
			$nursename="_____";
			$nurseSign="";
			if($NurseId !=""){
				$qr="select lname,fname,signature from users where usersId=$NurseId";
				$qrres=imw_query($qr);
				$record=imw_fetch_array($qrres);
				if(trim($record['lname'])) {
					$nursename=$record['lname'].','.$record['fname'];
				}
				$nurseSign=$record['signature'];
			}
			$Scrub2name="_____";
			if($scrubTechId2 !=""){
				$qrscrub2="select lname,fname from users where usersId=$scrubTechId2";
				$qrresscrub2=imw_query($qrscrub2);
				$recordscrub2=imw_fetch_array($qrresscrub2);
				$Scrub2name=$recordscrub2['lname'].','.$recordscrub2['fname']; 
			}
			
			$tablePdfPrint.=
			'<tr>
				<td style="width:350px;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:350px;"><b>Scrub Tech1</b>:&nbsp;'.$Scrub1name.'</td>
						</tr>
						<tr>
							<td style="width:350px;"><b>'.$RcNurse.' </b>:&nbsp;'.$nursename.'<br><b>Electronically Signed</b>:&nbsp;';
								if(trim($nurseSign)){
									$tablePdfPrint.="Yes";	
								}else{$tablePdfPrint.="No";}
							$tablePdfPrint.='</td>
						</tr>
					</table>
				</td>
				<td style="width:350px;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:350px;">';
							if($signNurse1Status=="Yes"){
								$tablePdfPrint.="<b>Nurse:&nbsp;</b>".$signNurse1Name;
								$tablePdfPrint.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$tablePdfPrint.="<br><b>Signature Date:&nbsp;</b>".$objManageData->getFullDtTmFormat($ViewOpRoomRecordRow["signNurse1DateTime"]);
							}else {
								$tablePdfPrint.="<b>Nurse:&nbsp;</b>________";
								$tablePdfPrint.="<br><b>Electronically Signed:&nbsp;</b>________";
								$tablePdfPrint.="<br><b>Signature Date:&nbsp;</b>________";
							}							
							$tablePdfPrint.='
							</td>
						</tr>
						<tr>
							<td style="width:350px;"><b>Scrub Tech2</b>:&nbsp;'.$Scrub2name.'</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="width:350px;">
					<table style="width:350px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:350px;">';
							if($verifiedbySurgeon=='Yes'){
								$tablePdfPrint.="<b>Surgeon:&nbsp;</b> Dr. ".$OpRoomSurgeonName;
								$tablePdfPrint.="<br><b>Electronically Signed:&nbsp;</b>Yes";
								$tablePdfPrint.="<br><b>Signature Date:&nbsp;</b>".$OpRoom_patientConfirmDos.' '.$verifiedbyNurseTime;
							}else {
								$tablePdfPrint.="<b>Surgeon:&nbsp;</b>________";
								$tablePdfPrint.="<br><b>Electronically Signed:&nbsp;</b>________";
								$tablePdfPrint.="<br><b>Signature Date:&nbsp;</b>________";
							}
							$tablePdfPrint.='
							</td>
						</tr>
					</table>
				</td>
				<td style="width:350px;">
					
				</td>
			</tr>
		 </table>';
		if($iol_ScanUpload!=''){
			$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload);
			imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom.jpg');
			
			$newSize=' height="100"';
			$priImageSize=array();
			if(file_exists('html2pdfnew/oproom.jpg')) {
				$priImageSize = getimagesize('html2pdfnew/oproom.jpg');
				if($priImageSize[0] > 395 && $priImageSize[1] < 840){
					$newSize = $objManageData->imageResize(680,400,500);						
					$priImageSize[0] = 500;
				}					
				elseif($priImageSize[1] > 840){
					$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
					$priImageSize[1] = 600;
				}
				else{					
					$newSize = $priImageSize[3];
				}							
				if($priImageSize[1] > 800 ){					
					echo '<page>'.$page_footer_html.'</page>';												
				}
			}
			
			$tablePdfPrint.='
			</page><page>'.$page_footer_html.'<table style="width:744px; text-align:center; border:1px solid #C0C0C0; " cellpadding="0" cellspacing="0">
				<tr>
					<td class="bdrbtm cbold bgcolor">IOL Scan / Upload</td>
				</tr>
				<tr>
					<td style="width:744px; text-align:center;" class="cbold" ><img src="../html2pdfnew/oproom.jpg" '.$newSize.'></td>
				</tr>
			</table>';
		}
		if($iol_ScanUpload2!=''){
			$bakImgResourceOproom1 = imagecreatefromstring($iol_ScanUpload2);
			imagejpeg($bakImgResourceOproom1,'html2pdfnew/oproom1.jpg');
			
			$priImageSize=array();
			if(file_exists('html2pdfnew/oproom1.jpg')) {
				$priImageSize = getimagesize('html2pdfnew/oproom1.jpg');
				$newSize = 'height="100"';
				if($priImageSize[0] > 395 && $priImageSize[1] < 840){
					$newSize = $objManageData->imageResize(680,400,300);						
					$priImageSize[0] = 300;
				}					
				elseif($priImageSize[1] > 840){
					$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],400);						
					$priImageSize[1] =400;
				}
				else{					
					$newSize = $priImageSize[3];
				}							
				if($priImageSize[1] > 800 ){					
					echo '<page>'.$page_footer_html.'</page>';														
				}
			}
			$tablePdfPrint.='
			<br><table style="width:744px; text-align:center; border:1px solid #C0C0C0; " cellpadding="0" cellspacing="0">
				
				<tr>
					<td style="width:744px; text-align:center;" class="cbold" ><img src="../html2pdfnew/oproom1.jpg" '.$newSize.'></td>
				</tr>
			</table>';
		}
	 	$tablePdfPrint.='</page>';
		$table.=$tablePdfPrint;
}
//=========================================================================//

//===============================Operative Report===========================//
	//VIEW RECORD FROM DATABASE
	$ViewoperativeQry = "select *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat from `operativereport` where confirmation_id='".$pConfId."'";
	$ViewoperativeRes = imw_query($ViewoperativeQry) or die(imw_error()); 
	$ViewoperativeNumRow = imw_num_rows($ViewoperativeRes);
	$ViewoperativeRow = imw_fetch_array($ViewoperativeRes); 
	$operative_surgeon_sign = $ViewoperativeRow["signature"];
	$operative_data = stripslashes($ViewoperativeRow["reportTemplate"]);
	$opReportFormStatus = $ViewoperativeRow["form_status"];
	
	$signSurgeon1Id = $ViewoperativeRow["signSurgeon1Id"];
	$signSurgeon1FirstName = $ViewoperativeRow["signSurgeon1FirstName"];
	$signSurgeon1MiddleName = $ViewoperativeRow["signSurgeon1MiddleName"];
	$signSurgeon1LastName = $ViewoperativeRow["signSurgeon1LastName"];
	$signSurgeon1Status = $ViewoperativeRow["signSurgeon1Status"];
	$operative_data = str_ireplace( '/surgerycenter/',$_SERVER['DOCUMENT_ROOT'].'/surgerycenter/', $operative_data);
	$operative_data = str_ireplace('/'.$surgeryCenterDirectoryName.'/new_html2pdf/',"",$operative_data);
//END VIEW RECORD FROM DATABASE

if($opReportFormStatus=='completed' || $opReportFormStatus=='not completed'){
	$table.='<page backbottom="5mm">'.$head_table;	
	$table.='<table style="width:700px;" cellpadding="0" cellspacing="0">
			<tr>	
				<td style="width:700px; height:3px;"></td>
			</tr>';
		$strip_p = (substr($operative_data,1,5) == 'table') ? '<p>' : '';
		if($operative_data!=""){
			$table.='
			<tr>
				<td style="width:700px; vertical-align:middle;" class="bgcolor bdrbtm cbold">Operative Report</td>
			</tr>
			<tr>
				<td style="width:700px;">'.strip_tags(nl2br($operative_data),' <img> <strong> <br> <table><tr><td><tbody>'.$strip_p).'</td>
			</tr>';
		}
			$table.='
			<tr>
				<td style="width:700px;">';
				if($signSurgeon1LastName!="" || $signSurgeon1FirstName!=''){	
					$table.='
						<b>Surgeon:&nbsp;</b>'.$signSurgeon1LastName.', '.$signSurgeon1FirstName.'
						<br><b>Electronically Signed:&nbsp;</b>'.$ViewoperativeRow['signSurgeon1Status'].'
						<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($ViewoperativeRow['signSurgeon1DateTime']);
				}else{
					$table.='
						<b>Surgeon:&nbsp;</b>______
						<br><b>Electronically Signed:&nbsp;</b>________
						<br><b>Signature Date:&nbsp;</b>________';
				}
			$table.='
				</td>					
			</tr>';
			//START IOL SCAN UPLOAD IMAGE
			
			/*if($post2OperativeReport=='Yes'){
				$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
				$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
				$iol_ScanUpload2 = $ViewOpRoomRecordRow["iol_ScanUpload2"];
			}*/
			//if($post2OperativeReport=='Yes'){
				if($iol_ScanUpload!=''){
					$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload);
					imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom.jpg');
					
					$newSize=' height="100"';
					$priImageSize=array();
					if(file_exists('html2pdfnew/oproom.jpg')) {
						$priImageSize = getimagesize('html2pdfnew/oproom.jpg');
						if($priImageSize[0] > 395 && $priImageSize[1] < 840){
							$newSize = $objManageData->imageResize(680,400,500);						
							$priImageSize[0] = 500;
						}					
						elseif($priImageSize[1] > 840){
							$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
							$priImageSize[1] = 600;
						}
						else{					
							$newSize = $priImageSize[3];
						}							
						if($priImageSize[1] > 800 ){					
							echo '<page></page>';												
						}
					}
					$table.='<tr><td style="width:700px;text-align:center;" class="bdrbtm"><img src="../html2pdfnew/oproom.jpg" '.$newSize.'></td></tr>';
				}
			
				if($iol_ScanUpload2!=''){
					$bakImgResourceOproom1 = imagecreatefromstring($iol_ScanUpload2);
					imagejpeg($bakImgResourceOproom1,'html2pdfnew/oproom1.jpg');
					
					$priImageSize=array();
					if(file_exists('html2pdfnew/oproom1.jpg')) {
						$priImageSize = getimagesize('html2pdfnew/oproom1.jpg');
						$newSize = 'height="100"';
						if($priImageSize[0] > 395 && $priImageSize[1] < 840){
							$newSize = $objManageData->imageResize(680,400,500);						
							$priImageSize[0] = 500;
						}					
						elseif($priImageSize[1] > 840){
							$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
							$priImageSize[1] = 600;
						}
						else{					
							$newSize = $priImageSize[3];
						}							
						if($priImageSize[1] > 800 ){					
							echo '<page></page>';												
						}
					}
					$table.='<tr><td style="width:700px;padding-top:20px;text-align:center;" class="bdrbtm"><img src="../html2pdfnew/oproom1.jpg" '.$newSize.'></td></tr>';
				}
			//}
		$table.='</table></page>';	
	}
//===========================================================================//

//======================Discharge Summary Sheet=============================//

$Qry="select *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat from dischargesummarysheet where confirmation_id=$pConfId";
$recordQry=imw_query($Qry);
$i = 0;
$DischargeResult=imw_fetch_array($recordQry);

$date= $DischargeResult['signSurgeon1DateTime']; 
$date_surgeon=explode(' ',$date);
$date_sign=explode('-',$date_surgeon[0]);
$date_surgeon_sign= $date_sign[1].'/'.$date_sign[2].'/'.$date_sign[0];
$disclaimer_txt = $DischargeResult['disclaimer_txt'];
$procedures_code_list= $DischargeResult['procedures_code'];
$disSummryFormStatus= $DischargeResult['form_status'];
$procedures_codes= array_filter(explode(',',$procedures_code_list)); 

$diag_ids_list=$DischargeResult['diag_ids'];
$diag_ids=explode(',',$diag_ids_list);
$diag_namesArr=explode('@@',$DischargeResult['diag_names']);
foreach($diag_ids as $_key => $diagID)
{
	$diag_names[$diagID] = $diag_namesArr[$_key];	
}
$diagids_length=count($diag_ids);

//START GETTING ICD10
$icd10_id_length = 0;
$icd10_code = $icd10_id = array();
if($DischargeResult['icd10_id']) {
	$icd10_code  = explode(',',$DischargeResult['icd10_code']);
	$icd10_id = explode(',',$DischargeResult['icd10_id']);
	$icd10_nameArr = explode('@@',$DischargeResult['icd10_name']);
	$icd10_id_length=count($icd10_id);

	foreach($icd10_id as $_key => $val)
	{
		$icd10_name[$val]	= $icd10_nameArr[$_key];
	}
}
//END GETTING ICD10

$procedures_list= $DischargeResult['procedures_name'];
if($procedures_list){
	$procedures =explode(',',$procedures_list);
}

$procNameArray = $procCodeNameArray = array();
$procedures_nameDB	=	$DischargeResult['procedures_name'];
$procedures_codeDB	=	$DischargeResult['procedures_code_name'];
$procNameExplode	=	array_filter(explode("!,!",$procedures_nameDB));
$procCodeNameExplode=	array_filter(explode("##",$procedures_codeDB));

if(is_array($procedures_codes) && count($procedures_codes) > 0)
{
	foreach($procedures_codes as $_key=>$_val)	
	{
		$procNameArray[$_val]		=	trim($procNameExplode[$_key]);
		$procCodeNameArray[$_val]	=	trim($procCodeNameExplode[$_key]);
	}
}

$procedures_length= count($procedures_codes);
$qry_procedure_category="Select proceduresCategoryId,name from procedurescategory";
	$res_procedure_category=imw_query($qry_procedure_category);
while($rowProc=imw_fetch_assoc($res_procedure_category)){
	$proc_id=$rowProc['proceduresCategoryId'];
	$procNameArr[$proc_id]=$rowProc['name'];
	
}
//print_r($DischargeResult);
$surgeon=$DischargeResult['signSurgeon1LastName'].','.$DischargeResult['signSurgeon1FirstName'];
if($disSummryFormStatus=='completed' || $disSummryFormStatus=='not completed'){
	$tableDSummery ='';
	$tableDSummery.='<page>'.$head_table;
	$tableDSummery.='
	<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2" style="width:740px;" class="fheader">Discharge Summary Sheet</td>
		</tr>
		<tr>
			<td style="width:370px;" class="bdrbtm">
				<table style="width:370px; border-right:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
					';
				if($procedures_length>0 && $procedures_codes[0]){		
					$procInsertArr=array();
					for($len=0;$len<$procedures_length;$len++)
					{
						$procedureQry="select * from procedures where procedureId = $procedures_codes[$len]";
						$proced= imw_query($procedureQry);
						$procedurelisting=@imw_fetch_array($proced);
						$procedurelistingNumRows=@imw_num_rows($proced);
						
						if($procNameArray[$procedurelisting['procedureId']] && $procedurelisting['name'] <> $procNameArray[$procedurelisting['procedureId']])
						{
							$procedurelisting['name'] = $procNameArray[$procedurelisting['procedureId']];
						}
						if($procCodeNameArray[$procedurelisting['procedureId']] && $procedurelisting['code'] <> $procCodeNameArray[$procedurelisting['procedureId']])
						{
							$procedurelisting['code'] = $procCodeNameArray[$procedurelisting['procedureId']];
						}
						
						$procedurecode[] = $procedurelisting['code'];
						$procedurename[] = $procedurelisting['name'];
						$procedureCatId[]= $procedurelisting['catId'];
						if($procNameArr[$procedureCatId[$len]] && !in_array($procedureCatId[$len],$procInsertArr)){
							$tableDSummery.='
								<tr>
									<td colspan="3" class="bdrbtm bgcolor bold">'.$procNameArr[$procedureCatId[$len]].'</td>
								</tr>
							';	
						}
						$procInsertArr[]=$procedureCatId[$len];
				$tableDSummery.=
					'<tr>	
						<td style="width:100px;" class="bdrbtm pl5">'.wordwrap($procedurecode[$len],12,"<br>",1).'</td>
						<td style="width:210px;" class="bdrbtm pl5">'.$procedurename[$len].'</td>
						<td style="width:30px;" class="bdrbtm pl5">Yes</td>
					</tr>';
					}
				}else{
					$tableDSummery.=
					'
					<tr>	
						<td colspan="3" class="bdrbtm pl5 bold bgcolor">Procedures</td>
					</tr>
					<tr>	
						<td style="width:100px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:210px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
					</tr>
					<tr>	
						<td style="width:100px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:210px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
					</tr>
					<tr>	
						<td style="width:100px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:210px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
					</tr>
					<tr>	
						<td style="width:100px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:210px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
					</tr>
					<tr>	
						<td style="width:100px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:210px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
					</tr>';
				}
				if(stripslashes($DischargeResult['otherMiscellaneous'])){
					$tableDSummery.='
						<tr>
							<td style="width:100px;" class="bdrbtm pl5">Other:</td>
							<td colspan="2" style="width:240px;" class="bdrbtm pl5">'.stripslashes($DischargeResult['otherMiscellaneous']).'</td>
						</tr>	
					';
				}
				if(stripslashes($DischargeResult['comment'])){
					$tableDSummery.='
						<tr>
							<td style="width:100px;" class="bdrbtm pl5">Comments:</td>
							<td colspan="2" style="width:240px;" class="bdrbtm pl5">'.stripslashes($DischargeResult['comment']).'</td>
						</tr>	
					';
				}
				
				
				$tableDSummery.=	
				'</table>	
			</td>
			<td valign="top" style="width:370px; vertical-align:text-top;" class="bdrbtm" >
				<table valign="top" style="width:370px; border-left:1px solid #C0C0C0;border-right:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="3" class="bold pl5 bdrbtm bgcolor">Diagnosis</td>
					</tr>';
					if($diagids_length>0 && $diag_ids[0]){
						for($c=0;$c<$diagids_length;$c++){
							$diagQry ="select * from diagnosis_tbl where diag_id= $diag_ids[$c]";
							$resDiag=imw_query( $diagQry);
							$diagnosis=@imw_fetch_array($resDiag);
							$diagcodes=$diagnosis['diag_code'];
							$diagcodeslist=explode(',',$diagcodes);
							if($diagcodeslist[1] <> $diag_names[$diagcodeslist[0]] && $diag_names[$diagcodeslist[0]])
							{
								$diagcodeslist[1] = $diag_names[$diagcodeslist[0]];	
							}
							$diagcode[]= $diagcodeslist[0];
							$diagdesc[]= $diagcodeslist[1];
							if($diagcodes!=''){ 
								$tableDSummery.=
								'<tr>	
									<td style="width:60px;" class="bdrbtm pl5">'.wordwrap($diagcode[$c],12,"<br>",1).'</td>
									<td style="width:240px;" class="bdrbtm pl5">'.wordwrap($diagdesc[$c],30,"<br>",1).'</td>
									<td style="width:30px;" class="bdrbtm pl5">Yes</td>
								</tr>';
							}
						}
					}else if($icd10_id_length>0){
						$icd10Qry ="select id, icd10_desc from icd10_data where id IN(".$DischargeResult['icd10_id'].")";
						$icd10Res = imw_query($icd10Qry) or die($icd10Qry.imw_error());
						while($icd10Row = imw_fetch_assoc($icd10Res)) {
							$db_icd10_id = $icd10Row['id'];	
							$db_icd10_desc = $icd10Row['icd10_desc'];
							if($db_icd10_desc <> $icd10_name[$db_icd10_id] && $icd10_name[$db_icd10_id])
							{
								$db_icd10_desc = $icd10_name[$db_icd10_id];
							}
							$db_icd10_desc_arr[$db_icd10_id] = $db_icd10_desc;
						}
						
						for($c=0;$c<$icd10_id_length;$c++){
							$tempCode = implode(", " ,explode("@@",$icd10_code[$c]));	
							$tableDSummery.=
							'<tr>	
								<td style="width:60px;" class="bdrbtm pl5">'.wordwrap($tempCode,12,"<br>",1).'</td>
								<td style="width:240px;" class="bdrbtm pl5">'.wordwrap($db_icd10_desc_arr[$icd10_id[$c]],30,"<br>",1).'</td>
								<td style="width:30px;" class="bdrbtm pl5">Yes</td>
							</tr>';
						}
					}else{
						$tableDSummery.=
						'<tr>	
							<td style="width:60px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:240px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
						</tr>
						<tr>	
							<td style="width:60px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:240px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
						</tr>
						<tr>	
							<td style="width:60px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:240px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
						</tr>
						<tr>	
							<td style="width:60px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:240px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
						</tr>
						<tr>	
							<td style="width:60px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:240px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
						</tr>';	
					}
					 $disAttached = $DischargeResult["disAttached"];
					$dis_ScanUpload = $DischargeResult["dis_ScanUpload"];
					$dis_ScanUpload2 = $DischargeResult["dis_ScanUpload2"];
					if($disAttached=='Yes'){
						$tableDSummery.='
							<tr>
								<td colspan="2" class="bdrbtm">See attached discharge Summary</td>
								<td style="width:30px;" class="bdrbtm pl5 cbold">Yes</td>
							</tr>
						';


						if($dis_ScanUpload!='' || $dis_ScanUpload2!=''){
						$tableDSummery.='
							<tr>
								<td colspan="3" class="bdrbtm pl5 cbold">Attached Discharge Summary</td>
							</tr>';
						$tableDSummery.='
							<tr>
								<td colspan="3" class="bdrbtm pl5 cbold" style="width:350px;">
									<table style="width:350px;" cellpadding="0" cellspacing="0">
										<tr>';
										if($dis_ScanUpload!='') {
											$bakImgResourceDischarge = imagecreatefromstring($dis_ScanUpload);
											imagejpeg($bakImgResourceDischarge,'html2pdfnew/disSummarySheet.jpg');
											$tableDSummery.='<td style="width:175px; text-align:center;"><img src="../html2pdfnew/disSummarySheet.jpg" style="height:100px;width:100px; border:1px solid #C0C0C0;"></td>';
										}
										if($dis_ScanUpload2!='') {
											$bakImgResourceDischarge1 = imagecreatefromstring($dis_ScanUpload2);
											imagejpeg($bakImgResourceDischarge1,'html2pdfnew/disSummarySheet1.jpg');
											$tableDSummery.='<td style="width:175px; text-align:center; border:1px solid #C0C0C0;"><img src="../html2pdfnew/disSummarySheet1.jpg" style="height:100px;width:100px; float:left;"></td>';
										}
								$tableDSummery.='			
										</tr>	
									</table>
								</td>		
							</tr>';
						}
					}
					if(stripslashes($DischargeResult['other1'])){
						$tableDSummery.='
						<tr>
							<td style="width:60px;" class="bdrbtm">Other1:</td>
							<td colspan="2" class="bdrbtm" style="width:270px;">'.stripslashes($DischargeResult['other1']).'</td>	
						</tr>';
					}
					if(stripslashes($DischargeResult['other2'])){
						$tableDSummery.='
						<tr>
							<td style="width:60px;" class="bdrbtm">Other2:</td>
							<td colspan="2" style="width:270px;" class="bdrbtm">'.stripslashes($DischargeResult['other2']).'</td>	
						</tr>';
					}
					//START GET ALLERGIES VALUE
					if(count($getAllergiesName)>0){
						$tableDSummery.='
							<tr>
								<td colspan="3" class="cbold bdrbtm bgcolor">Allergies/Drug Reaction</td>
							</tr>
							<tr>
								<td colspan="3" style="width:350px;">
									<table style="width:350px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:170px;" class="bdrbtm bold pl5">Name</td>
											<td style="width:170px;" class="bdrbtm bold pl5">Reaction</td>
										</tr>';
										foreach($getAllergiesName as $keyDis => $allergiesNameDish){
											$tableDSummery.='
											<tr>
												<td style="width:170px; " class="bdrbtm pl5">'.stripslashes(htmlentities($allergiesNameDish)).'</td>
												<td style="width:170px;border-left:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($getAllergiesRect[$keyDis])).'</td>	
											</tr>';
										}
									$tableDSummery.='	
									</table>
								</td>
							</tr>
						';	
					}
					
					
					
					
					//START IOL SCAN UPLOAD IMAGE
					if($iol_ScanUpload!='' || $iol_ScanUpload2!=''){
						$tableDSummery.='
							<tr>
								<td style="width:350px;" colspan="3" class="bdrbtm cbold">IOL Scanned Image</td>
							</tr>
							<tr>
								<td colspan="3">
									<table style="width:350px;" cellpadding="0" cellspacing="0">
										<tr>
							';
							if($iol_ScanUpload!=''){
								$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload);
								imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom.jpg');
								if(file_exists("html2pdfnew/oproom.jpg")){
									$tableDSummery.='
											<td style="width:175px;text-align:center; border:1px solid #C0C0C0;">
												<img src="../html2pdfnew/oproom.jpg" style="width:100px; height:100px;">
											</td>
										';
								}
											
							}
							if($iol_ScanUpload2!=''){
								$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload2);
								imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom1.jpg');
								if(file_exists("html2pdfnew/oproom.jpg")){
									$tableDSummery.='
												<td style="width:175px;text-align:center; border:1px solid #C0C0C0;">
													<img src="../html2pdfnew/oproom1.jpg" style="width:100px; height:100px;">
												</td>';
								}
											
							}
							$tableDSummery.='
								</tr>
							</table>
							</td>
						</tr>';
					}
					
				$tableDSummery.='
				</table>				
			</td>
		</tr>
		<tr>
			<td colspan="2" style="width:740px">
			<table style="width:740px;" cellpadding="0" cellspacing="0">';
			// Super Bill Records for facility | Surgeon | Anesthesia
			$p_array = array('Surgeon' => 2,'Facility' => 3, 'Anesthesia' => 1);
			foreach($p_array as $dsTitle => $buType)
			{
				$superBillQuery		=	"SELECT sb.* FROM superbill_tbl sb 
											INNER JOIN procedures pr ON(pr.procedureId = sb.cpt_id)
											INNER JOIN procedurescategory prc ON(prc.proceduresCategoryId = pr.catId)
											WHERE sb.confirmation_id = '".$pConfId."'
											AND sb.deleted = '0'
											AND sb.bill_user_type= '".$buType."'
											ORDER BY prc.name = 'G-Codes' DESC, sb.cpt_code";
					$superBillSql		=	imw_query($superBillQuery) or die(imw_error());
					$superBillNum		=	imw_num_rows($superBillSql);
					if($superBillNum > 0 )
					{
						$tableDSummery.='
							<tr>
								<td class="bdrbtm bold bgcolor" style="width:750px;">Discharge Summary ('.$dsTitle.')</td>
							</tr>
							';
						$tableDSummery.='
							<tr>
								<td style="width:750px;">
									<table style="width:750px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:100px;" class="bdrbtm bold pl5">CPT&nbsp;Codes</td>
											<td style="width:50px;" class="bdrbtm bold pl5">Unit</td>
											<td style="width:380px;" class="bdrbtm bold pl5">Dx Codes</td>
											<td style="width:50px;" class="bdrbtm bold pl5">Mod1</td>
											<td style="width:50px;" class="bdrbtm bold pl5">Mod2</td>
											<td style="width:40px;" class="bdrbtm bold pl5">Mod3</td>
										</tr>';	
										while( $superBillRow = imw_fetch_object($superBillSql))	
										{
											$DxCodes	=	($icd10_id_length > 0 ) ? $superBillRow->dxcode_icd10 : $superBillRow->dxcode_icd9;
											$DxCodes	=	str_replace(",",", ",$DxCodes);
											
											$tableDSummery.='
													<tr>
															<td class="bdrbtm pl5">'.$superBillRow->cpt_code.'</td>
															<td class="bdrbtm pl5">'.$superBillRow->quantity.'</td>
															<td class="bdrbtm pl5" style="width:380px;">'.$DxCodes.'</td>
															<td class="bdrbtm pl5">'.$superBillRow->modifier1.'</td>
															<td class="bdrbtm pl5">'.$superBillRow->modifier2.'</td>
															<td class="bdrbtm pl5">'.$superBillRow->modifier3.'</td>
													</tr>';	
										}
										$tableDSummery.='	
									</table>
								</td>
							</tr>
						';		
							
					} 
			}
			
			$tableDSummery.='</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="width:740px;" class="bdrbtm">
				I certify that the diagnosis and procedures performed are accurate and complete to the best of my knowledge ';
				if($DischargeResult['surgeon_knowledge']!=''){
					$tableDSummery.='<b>'.$DischargeResult['surgeon_knowledge'].'</b>';
				}
			$tableDSummery.=
			'</td>
	 	</tr>'.($disclaimer_txt ? '<tr><td colspan="2" style="width:740px;" class="bdrbtm">'.$disclaimer_txt.'</td></tr>':'').'
		<tr>
			<td style="width:370px;">';
			if($DischargeResult['signSurgeon1Status']){	
				$tableDSummery.='
					<b>Surgeon:&nbsp;</b>'.$surgeon.'
					<br><b>Electronically Signed:&nbsp;</b>'.$DischargeResult['signSurgeon1Status'].'
					<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($DischargeResult['signSurgeon1DateTime']);
					
			}else{
				$tableDSummery.='
					<b>Surgeon:&nbsp;</b>______
					<br><b>Electronically Signed:&nbsp;</b>________
					<br><b>Signature Date:&nbsp;</b>________';
			}
		$tableDSummery.='
			</td>	
			<td style="width:370px; text-align:right;">';
			if($date_surgeon_sign && $date_surgeon_sign!="00/00/0000"){	
				$tableDSummery.='Date:&nbsp;'.$date_surgeon_sign.'&nbsp;&nbsp;';
					
			}else{
				$tableDSummery.='Date:&nbsp;_______&nbsp;&nbsp;';
			}
		$tableDSummery.='
			</td>					
		 </tr>
	 </table>
	 ';
	 if($disAttached=='Yes') {
		$tableDSummery.=' </page><page>'.$page_footer_html.'
	 <table style="width:740px; border:apx solid #C0C0C0;" cellpadding="0" cellspacing="0">';
			if($dis_ScanUpload!='' || $dis_ScanUpload2!=''){
				$tableDSummery.='<tr><td style="width:700px;" class="cbold bdrbtm bgcolor">Attached Discharge Summary</td></tr>';
			}
			
			if($dis_ScanUpload!=''){
				$bakImgResourceDischarge = imagecreatefromstring($dis_ScanUpload);
				imagejpeg($bakImgResourceDischarge,'html2pdfnew/disSummarySheet.jpg');
				$newSize=' width="150" height="100"';
				$priImageSize=array();
				if(file_exists('html2pdfnew/disSummarySheet.jpg')) {
					$priImageSize = getimagesize('html2pdfnew/disSummarySheet.jpg');
					if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						$newSize = $objManageData->imageResize(680,400,400);						
						$priImageSize[0] = 500;
					}					
					elseif($priImageSize[1] > 840){
						$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],500);						
						$priImageSize[1] = 600;
					}
					else{					
						$newSize = $priImageSize[3];
					}							
					if($priImageSize[1] > 800 ){					
						echo '</page><page>'.$page_footer_html;
					}
				}
				$tableDSummery.='<tr><td style="width:700px;text-align:center;" class="bdrbtm"><img src="../html2pdfnew/disSummarySheet.jpg" '.$newSize.'></td></tr>';
			}
		
			if($dis_ScanUpload2!=''){
				$bakImgResourceDischarge1 = imagecreatefromstring($dis_ScanUpload2);
				imagejpeg($bakImgResourceDischarge1,'html2pdfnew/disSummarySheet1.jpg');
				
				$priImageSize=array();
				if(file_exists('html2pdfnew/disSummarySheet1.jpg')) {
					$priImageSize = getimagesize('html2pdfnew/disSummarySheet1.jpg');
					$newSize = ' width="150" height="100"';
					if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						$newSize = $objManageData->imageResize(680,400,400);						
						$priImageSize[0] = 500;
					}					
					elseif($priImageSize[1] > 840){
						$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],500);						
						$priImageSize[1] = 600;
					}
					else{					
						$newSize = $priImageSize[3];
					}							
					if($priImageSize[1] > 800 ){					
						echo '</page><page>'.$page_footer_html;												
					}
				}
				$tableDSummery.='<tr><td style="width:700px; text-align:center;" class="bdrbtm"><img src="../html2pdfnew/disSummarySheet1.jpg" '.$newSize.'></td></tr>';
			}	
		$tableDSummery.='
			</table>
			 ';			
		}
		//echo 'hlo'.$ViewOpRoomRecordNumRow;	die();
	if($iol_ScanUpload!='' || $iol_ScanUpload2!='') {
		$tableDSummery.='</page><page>'.$page_footer_html.'
	 <table style="width:740px; border:apx solid #C0C0C0;" cellpadding="0" cellspacing="0">';
			if($iol_ScanUpload!='' || $iol_ScanUpload2!=''){
				$tableDSummery.='<tr><td style="width:700px; text-align:center;" class="bdrbtm bgcolor">IOL Scanned Image</td></tr>';
			}
			
			if($iol_ScanUpload!=''){
				$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload);
				imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom.jpg');
				
				$newSize=' height="100"';
				$priImageSize=array();
				if(file_exists('html2pdfnew/oproom.jpg')) {
					$priImageSize = getimagesize('html2pdfnew/oproom.jpg');
					if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						$newSize = $objManageData->imageResize(680,400,400);						
						$priImageSize[0] = 400;
					}					
					elseif($priImageSize[1] > 840){
						$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],500);						
						$priImageSize[1] = 500;
					}
					else{					
						$newSize = $priImageSize[3];
					}							
					if($priImageSize[1] > 800 ){					
						echo '</page><page>'.$page_footer_html;												
					}
				}
				$tableDSummery.='<tr><td style="width:700px; text-align:center;" class="bdrbtm"><img src="../html2pdfnew/oproom.jpg" '.$newSize.'></td></tr>';
			}
		
			if($iol_ScanUpload2!=''){
				$bakImgResourceOproom1 = imagecreatefromstring($iol_ScanUpload2);
				imagejpeg($bakImgResourceOproom1,'html2pdfnew/oproom1.jpg');
				
				$priImageSize=array();
				if(file_exists('html2pdfnew/oproom1.jpg')) {
					$priImageSize = getimagesize('html2pdfnew/oproom1.jpg');
					$newSize = 'height="100"';
					if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						$newSize = $objManageData->imageResize(680,400,400);						
						$priImageSize[0] = 400;
					}					
					elseif($priImageSize[1] > 840){
						$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],500);						
						$priImageSize[1] = 500;
					}
					else{					
						$newSize = $priImageSize[3];
					}							
					if($priImageSize[1] > 800 ){					
						echo '</page><page>'.$page_footer_html;												
					}
				}
				$tableDSummery.='<tr><td style="width:700px; text-align:center;" class="bdrbtm"><img src="../html2pdfnew/oproom1.jpg" '.$newSize.'></td></tr>';
			}
			$tableDSummery.='</table>';		
		}
		$tableDSummery.='</page>';
		$table.=$tableDSummery;
	}
//==========================================================================//

//========================Instruction Sheet================================//
//GETTING PATIENT CONFIRMATION DETAILS
	$confirmationDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if(count($confirmationDetails)>0){
		@extract($confirmationDetails);
		if(!$patient_id){
			$patient_id = $patientId;
		}
	}
	if($surgeonId<>"") {
			
			$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' and del_status=''";
			$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
			while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
				$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
			}
			if(is_array($surgeonProfileIdArr)){
				$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
			}else {
				$surgeonProfileIdImplode = 0;
			}
			$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
			$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
			$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
			if($selectSurgeonProcedureNumRow>0) {
				while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
					$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
					if($patient_primary_procedure_id == $surgeonProfileProcedureId) {
						$instructionSheetFound = "true";
						$instructionSheetId = $selectSurgeonProcedureRow['instructionSheetId'];
					}		
				}
			}	
			//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
			//FROM SURGEON'S DEFAULT PROFILE 
				/*if($instructionSheetFound<>"true") {
					$selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' AND defaultProfile = '1'";
					$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
					while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
						$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
					}
					if(is_array($surgeonProfileIdArr)){
						$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
					}else {
						$surgeonProfileIdImplode = 0;
					}
					$selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
					$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
					$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
					if($selectSurgeonProcedureNumRow>0) {
						$selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes);
							$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
							$instructionSheetId = $selectSurgeonProcedureRow['instructionSheetId'];
							
					}
				}	*/
			//IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
			//FROM SURGEON'S DEFAULT PROFILE 
				
		
		}
		
	//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
	
	//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE

	// GETTING INSTRUCTION SHEET DETAILS	
		//GETTING IF ALREADY EXISIS
			$instDetails = $objManageData->getRowRecord('patient_instruction_sheet', 'patient_confirmation_id', $pConfId, "", "", " *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat, date_format(signWitness1DateTime,'%m-%d-%Y %h:%i %p') as signWitness1DateTimeFormat");
			if($instDetails){
				$patient_instruction_id = $instDetails->patient_instruction_id;
				$instSheetFormStatus=$instDetails->form_status;
				if($instructionData=='') {
					$instructionData = stripslashes($instDetails->instruction_sheet_data);
				}
				$signSurgeon1Activate = $instDetails->signSurgeon1Activate;
				$signSurgeon1InstructionId = $instDetails->signSurgeon1Id;
				$Surgeon1InstructionName = $instDetails->signSurgeon1LastName.", ".$instDetails->signSurgeon1FirstName." ".$instDetails->signSurgeon1MiddleName;
				$Surgeon1InstructionSignOnFileStatus = $instDetails->signSurgeon1Status;
				
				$signNurseActivate = $instDetails->signNurseActivate;
				$signNurseInstructionId = $instDetails->signNurseId;
				$NurseInstructionNameShow = $instDetails->signNurseLastName.", ".$instDetails->signNurseFirstName." ".$instDetails->signNurseMiddleName;
				$NurseInstructionSignOnFileStatus = $instDetails->signNurseStatus;
					
				$signWitness1Activate 		= $instDetails->signWitness1Activate;
				$signWitness1Id 			= $instDetails->signWitness1Id;
				$WitnessInstructionNameShow = $instDetails->signWitness1LastName.", ".$instDetails->signWitness1FirstName." ".$instDetails->signWitness1MiddleName;
				$signWitness1Status 		= $instDetails->signWitness1Status;
					
			
				if($signSurgeon1Activate=='yes' || $signNurseActivate=='yes') {
					$instructionData.='
						<table style="width:700px;"  cellpadding="0" cellspacing="0">
							<tr valign="top">
								<td style="width:400px;">
						';
							if($signSurgeon1Activate=='yes' && $signSurgeon1InstructionId) { 
								$instructionData.='
												<b>Surgeon:</b> Dr '.$Surgeon1InstructionName.'
												<br><b>Electronically Signed:&nbsp;</b>'.$instDetails->signSurgeon1Status.'
												<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($instDetails->signSurgeon1DateTime);
							}else{
								$instructionData.='
									<b>Surgeon:&nbsp;</b>______
									<br><b>Electronically Signed:&nbsp;</b>________
									<br><b>Signature Date:&nbsp;</b>________';
							}
							$instructionData.='</td>
							<td style="width:300px;">
							';
							if($signNurseActivate=='yes' && $signNurseInstructionId) { 
								$instructionData.='
												<b>Nurse:</b> '.$NurseInstructionNameShow.'
												<br><b>Electronically Signed:&nbsp;</b>'.$instDetails->signNurseStatus.'
												<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($instDetails->signNurseDateTime);
								
								
							}else{
								$instructionData.='
									<b>Nurse:&nbsp;</b>______
									<br><b>Electronically Signed:&nbsp;</b>________
									<br><b>Signature Date:&nbsp;</b>________';
							}

					$instructionData.='
								</td>
							</tr>
						</table>';
				}
			
			
			}
			//	CODE FOR ANESTHESIA AND WITNESS SIGN
			
			if($signAnesthesia1Activate=='yes' || $signWitness1Activate=='yes') {
			$instructionData.='
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td nowrap align="left" class="text_10" valign="middle" style="cursor:hand;" >
				';
					if($signWitness1Activate=='yes' && $signWitness1Id) { 
						$instructionData.='
										<b>Witness:</b> '.$WitnessInstructionNameShow.'
										<br><b>Electronically Signed:&nbsp;</b>'.$instDetails->signWitness1Status.'
										<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($instDetails->signWitness1DateTime);
					}else{
						$instructionData.='
							<b>Witness:&nbsp;</b>______
							<br><b>Electronically Signed:&nbsp;</b>________
							<br><b>Signature Date:&nbsp;</b>________';
					}

			$instructionData.='
						</td>
					</tr>
				</table>';
		}
		//END CODE FOR ANESTHESIA AND WITNESS SIGN	
				
		//GETTING IF ALREADY EXISIS	
		
		//GETTING IF instructionData DOES NOT ALREADY EXISIS	
			if($instructionData=='') { 
				$instructionDataStatus='false';
				$instructionDetails = $objManageData->getRowRecord('instruction_template', 'instruction_id', $instructionSheetId);
				$instructionData = stripslashes($instructionDetails->instruction_desc);
			}	
		//GETTING IF instructionData DOES NOT ALREADY EXISIS
		
	
	
	
	// GETTING INSTRUCTION SHEET DETAILS
//GETTING PATIENT CONFIRMATION DETAILS

//GETTING PATIENT DETAILS
	if(!patient_id) {
		$patient_id = $_REQUEST['patient_id'];
	}
	$patientDetails = $objManageData->getExtractRecord('patient_data_tbl', 'patient_id', $patient_id);
	if(count($patientDetails)>0){
		@extract($patientDetails);
	}	

	$instruction_patientNameDobTemp = $date_of_birth;
	$instruction_patientNameDob_split = explode("-",$instruction_patientNameDobTemp);
	$instruction_patientNameDob = $instruction_patientNameDob_split[1]."-".$instruction_patientNameDob_split[2]."-".$instruction_patientNameDob_split[0];
	
	//SET DOS FROM patientConfirmation TABLE
		$instruction_patientConfirmDosTemp = $dos;
		$instruction_patientConfirmDos_split = explode("-",$instruction_patientConfirmDosTemp);
		$instruction_patientConfirmDos = $instruction_patientConfirmDos_split[1]."-".$instruction_patientConfirmDos_split[2]."-".$instruction_patientConfirmDos_split[0];
	//END SET DOS FROM patientConfirmation TABLE
	
	//FETCH DATA FROM OPEARINGROOMRECORD TABLE
		$preopdiagnosis= $ViewOpRoomRecordRow["preOpDiagnosis"];
		$postopdiagnosis= $ViewOpRoomRecordRow["postOpDiagnosis"];
		if(trim($postopdiagnosis)=="") {
			$postopdiagnosis = $preopdiagnosis;
		}
	// END FETCH DATA FROM OPEARINGROOMRECORD TABLE
	
	$instructionData = str_ireplace("&ldquo;","&quot;",$instructionData);
	$instructionData = str_ireplace("&rdquo;","&quot;",$instructionData);
	$instructionData = str_ireplace( '{PATIENT FIRST NAME}', '<b>'.ucfirst($patient_fname).'</b>', $instructionData);
	$instructionData = str_ireplace( '{MIDDLE INITIAL}', '<b>'.ucfirst(substr($patient_mname, 0, 1)).'</b>', $instructionData);
	$instructionData = str_ireplace( '{LAST NAME}', '<b>'.ucfirst($patient_lname).'</b>', $instructionData);
	$instructionData = str_ireplace( '{DOB}', '<b>'.$instruction_patientNameDob.'</b>', $instructionData);
	$instructionData = str_ireplace( '{DOS}', '<b>'.$instruction_patientConfirmDos.'</b>', $instructionData);
	$instructionData = str_ireplace( '{SURGEON NAME}', '<b>'.ucfirst($surgeon_name).'</b>', $instructionData);
	$instructionData = str_ireplace( '{ARRIVAL TIME}', '<b>'.$arrivalTime.'</b>', $instructionData);	

	$instructionData= str_ireplace("{SIGNATURE}"," ",$instructionData);
	$instructionData= str_ireplace("{Surgeon's Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Surgeon's&nbsp;Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Surgeon&#39;s Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Surgeon&#39;s&nbsp;Signature}"," ",$instructionData);	
	$instructionData= str_ireplace("{Nurse's Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Nurse's&nbsp;Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Nurse&#39;s Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Nurse&#39;s&nbsp;Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Witness's Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Witness's&nbsp;Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Witness&#39;s Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Witness&#39;s&nbsp;Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Witness Signature}"," ",$instructionData);
	$instructionData= str_ireplace("{Witness&nbsp;Signature}"," ",$instructionData);	
	$instructionData = str_ireplace('SigPlus_images/',"../SigPlus_images/",$instructionData);
	$instructionData = str_ireplace('html2pdfnew/',"../html2pdfnew/",$instructionData);
	$instructionData= str_ireplace("{TEXTBOX_XSMALL}"," ",$instructionData);
	$instructionData= str_ireplace("{TEXTBOX_SMALL}"," ",$instructionData);
	$instructionData= str_ireplace("{TEXTBOX_MEDIUM}"," ",$instructionData);
	$instructionData= str_ireplace("{TEXTBOX_LARGE}"," ",$instructionData);
	$instructionData= str_ireplace("{ASC NAME}",$_SESSION['loginUserFacilityName'],$instructionData);
	$instructionData= str_ireplace("{ASC ADDRESS}",$ascInfoArr[0],$instructionData);
	$instructionData= str_ireplace("{ASC PHONE}",$ascInfoArr[1],$instructionData);
		

	//START SET TEXT BOX VALUES
		$instructionData = str_ireplace('<input type="text" name="xsmall" value="','',$instructionData);
		$instructionData = str_ireplace('" size="1" >','',$instructionData);
	 
		$instructionData = str_ireplace('<input type="text" name="small" value="','',$instructionData);
		$instructionData = str_ireplace('" size="30" >','',$instructionData);
	
		$instructionData = str_ireplace('<input type="text" name="medium" value="','',$instructionData);
		$instructionData = str_ireplace('" size="60" >','',$instructionData);

		//REPLACE MULTIPLE VERY SMALL TEXTBOX
		$verySmallTextBoxExplode = explode('name="xsmall',$instructionData);
		for($i=1;$i<count($verySmallTextBoxExplode);$i++) {
			
			$verySmallTextbox = 'xsmall'.$i;
			$instructionData = str_ireplace('<input type="text"  name="'.$verySmallTextbox.'" value="','',$instructionData);
			$instructionData = str_ireplace('" size="1"  maxlength="1">','',$instructionData);
		}
		//REPLACE MULTIPLE VERY SMALL TEXTBOX
		
		//REPLACE MULTIPLE SMALL TEXTBOX
		$smallTextBoxExplode = explode('name="small',$instructionData);
		for($j=1;$j<count($smallTextBoxExplode);$j++) {
			
			$smallTextbox = 'small'.$j;
			$instructionData = str_ireplace('<input type="text"  name="'.$smallTextbox.'" value="','',$instructionData);
			$instructionData = str_ireplace('" size="30"  maxlength="30">','',$instructionData);
		}
		//REPLACE MULTIPLE SMALL TEXTBOX
		
		//REPLACE MULTIPLE MEDIUM TEXTBOX
		$mediumTextBoxExplode = explode('name="medium',$instructionData);
		for($k=1;$k<count($mediumTextBoxExplode);$k++) {
			
			$mediumTextbox = 'medium'.$k;
			$instructionData = str_ireplace('<input type="text"  name="'.$mediumTextbox.'" value="','',$instructionData);
			$instructionData = str_ireplace('" size="60"  maxlength="60">','',$instructionData);
		}
		//REPLACE MULTIPLE MEDIUM TEXTBOX

	//END SET TEXT BOX VALUES

	
//GETTING PATIENT DETAILS

//GETTING SURGEONS DETAILS
	$surgeonDetails = $objManageData->getExtractRecord('users', 'usersId', $surgeonId);
	if(is_array($surgeonDetails)){
		@extract($surgeonDetails);
	}	
//GETTING SURGEONS DETAILS

//GETTING SITE, PRIMARY, SECONDARY PROCEDURE DETAILS
	if($site=='1'){	$site = 'left';	}
	if($site=='2'){	$site = 'right';}
	if($site=='3'){	$site = 'both';	}
	$instructionData = str_ireplace( '{SITE}', '<b>'.ucfirst($site.' Site').'</b>', $instructionData);
	$instructionData = str_ireplace( '{PROCEDURE}', '<b>'.ucfirst($patient_primary_procedure).'</b>', $instructionData);
	$instructionData = str_ireplace( '{SECONDARY PROCEDURE}', '<b>'.ucfirst($patient_secondary_procedure).'</b>', $instructionData);
	$instructionData = str_ireplace( '{PRE-OP DIAGNOSIS}','<b>'.$preopdiagnosis.'</b>',$instructionData);
	$instructionData = str_ireplace( '{POST-OP DIAGNOSIS}','<b>'.$postopdiagnosis.'</b>',$instructionData);
	$instructionData = str_ireplace( '{DATE}','<b>'.date('m-d-Y').'</b>', $instructionData);
	//$instructionData = str_ireplace( '/surgerycenter/',$_SERVER['DOCUMENT_ROOT'].'/surgerycenter/', $instructionData);
	$instructionData = str_ireplace('/'.$surgeryCenterDirectoryName.'/new_html2pdf/',"",$instructionData);
	
	
//GETTING SITE DETAILS
if($instSheetFormStatus=='completed' || $instSheetFormStatus=='not completed'){
	$table_main='';
	$table_main.='<page backbottom="5mm">'.$head_table;	
	$table_main.='<table style="width:740px;" cellpadding="0" cellspacing="0">
				<tr>	
					<td style="width:740px;" class="fheader">Instruction Sheet</td>
				</tr>';
			
			if($instructionData!=""){
				$table_main.='
						<tr>
							<td style="width:740px;" class="bgcolor bdrbtm cbold">Patient Instruction Sheet</td>
						</tr>
						<tr>
							<td style="width:740px;">'.strip_tags($instructionData,'<img> <br> <b> <p> <div> <table> <tbody> <tr> <td> ').'</td>
						</tr>';
			}
	$table_main.='</table></page>';	
		
	$table_main=str_ireplace('<p style="text-align: left"></p>','',$table_main);
	$table_main=str_ireplace('<p style="text-align: left">  </p>','',$table_main);
	$table_main=str_ireplace('<p style="text-align: left"> </p>','',$table_main);
	$table_main=str_ireplace('<p style="text-align: left">&nbsp;</p>','',$table_main);
	$table_main=str_ireplace('<p>&nbsp;</p>','',$table_main);
	$table_main=str_ireplace('<p> </p>','',$table_main);
	$table_main=str_ireplace('<p style="text-align:center">&nbsp;</p>','',$table_main);
	$table_main=str_ireplace('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;','',$table_main);   	
	$table.=$table_main;
}
//========================================================================//


//=====================================Medication Reconciliation Sheet Record=====================================//
if(file_exists("medication_reconciliation_sheet_pdf_content.php")){
	$table_main="";
	include_once("medication_reconciliation_sheet_pdf_content.php");
	if($table_main){$table.='<page>'.$table_main.'</page>';}
}
//================================================================================================//


//=====================================Transfer & Followup Record=====================================//
if(file_exists("transfer_followups_pdf_content.php")){
	$table_main="";
	include_once("transfer_followups_pdf_content.php");
	if($table_main){$table.='<page>'.$table_main.'</page>';}
}
//================================================================================================//


//============================Amendments Notes===========================//

	
			
			
$getAmendments = $objManageData->getArrayRecords('amendment', 'confirmation_id', $pConfId);

if(is_array($getAmendments)){
	$tableAmdNote='';
	$tableAmdNote.='<page>'.$head_table;
	$tableAmdNote.='
			<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
				<tr>	
					<td colspan="5" style="width:740px;" class="fheader">Amendments</td>
				</tr>
				<tr>
					<td style="width:240px; height:20px;" class="bold pl5  bgcolor">Amendment Notes</td>
					<td style="width:100px; height:20px;" class="bold pl5  bgcolor">Who</td>
					<td style="width:140px; height:20px;" class="bold pl5  bgcolor">Created by</td>
					<td style="width:100px; height:20px;" class="bold pl5  bgcolor">Date</td>
					<td style="width:100px; height:20px;" class="bold pl5  bgcolor">Time</td>
				</tr>';
	foreach($getAmendments as $key => $amendment){
		$amendmentId = $amendment->amendmentId;
		$amendmentNotes = $amendment->notes;
		$dateAmendment = $objManageData->changeDateMDY($amendment->dateAmendment);
		$timeAmendment = $amendment->timeAmendment;
		$userIdAmendment = $amendment->userId;
		$form_status = $amendment->form_status;
		
		
		$getUserNameQry = "SELECT * FROM users
				WHERE usersId = '$userIdAmendment'";
		
		$getUserNameRes = imw_query($getUserNameQry) or die(imw_error());
		$getUserNameRow = imw_fetch_array($getUserNameRes);
		 $getUserFname = $getUserNameRow['fname'];
		 $getUserMname = $getUserNameRow['mname'];
		 $getUserLname = $getUserNameRow['lname'];
		 $getUserName = $getUserFname." ".$getUserMname." ".$getUserLname;
		 $getUserType = $getUserNameRow['user_type'];
		$getUserTypeLabel = ($getUserType == 'Anesthesiologist') ? 'Anesthesia Provider' : $getUserType ;	
		
		//CODE TO SET AMENDMENT TIME
			if($timeAmendment=="00:00:00" || $timeAmendment=="") {
				$timeAmendment="";
			}else {			
				$time_split2 = explode(":",$timeAmendment);
				if($time_split2[0]=='24') { //to correct previously saved records
					$timeAmendment = "12".":".$time_split2[1].":".$time_split2[2];
				}
				//$timeAmendment = date('h:i A',strtotime($timeAmendment));
				$timeAmendment = $objManageData->getTmFormat($timeAmendment);
			}
			/*
			if($time_split2[0]>12) {
				$am_pm2 = "PM";
			}else {
				$am_pm2 = "AM";
			}
			if($time_split2[0]>=13) {
				$time_split2[0] = $time_split2[0]-12;
				if(strlen($time_split2[0]) == 1) {
					$time_split2[0] = "0".$time_split2[0];
				}
			}else {
				//DO NOTHNING
			}
			$timeAmendment = $time_split2[0].":".$time_split2[1]." ".$am_pm2;
			*/
		//END CODE TO SET AMENDMENT TIME				
			
			
			$tableAmdNote.='
				<tr>
					<td style="width:240px; " class="bold pl5 bdrbtm">'.stripslashes($amendmentNotes).'</td>
					<td style="width:100px; " class="bold pl5 bdrbtm">'.$getUserTypeLabel.'</td>
					<td style="width:140px; " class="bold pl5 bdrbtm">'.stripslashes($getUserName).'</td>
					<td style="width:100px; " class="bold pl5 bdrbtm">'.$dateAmendment.'</td>
					<td style="width:100px; " class="bold pl5 bdrbtm">'.$timeAmendment.'</td>
			</tr>';
	}
	$tableAmdNote.='</table></page>';
	$table.=$tableAmdNote;
}
	
//======================================================================//

//============================Progress Notes===========================//


$progress_notes_query = "SELECT tblprogress_report.intProgressID, tblprogress_report.txtNote,
						tblprogress_report.confirmation_id, users.fname, users.mname, users.lname, users.user_type,
						tblprogress_report.dtDateTime, tblprogress_report.tTime
						FROM tblprogress_report, users
						WHERE tblprogress_report.confirmation_id = '$pConfId'  AND users.usersId = tblprogress_report.usersId
						ORDER BY dtDateTime DESC, tTime DESC";
$resourceProgressNotes = imw_query($progress_notes_query) or die(imw_error());
$totalRows_Progress_notes = imw_num_rows($resourceProgressNotes);

if($totalRows_Progress_notes > 0) {
	
	$lable="Progress Notes";
	$table_main4='<page>'.$head_table."\n";
	
	$table_main4.='<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">';				
	$table_main4.='	<tr>
						<td colspan="5" align="center" height="7"><strong>'.$lable.'</strong></td>
					</tr>';
	while ($row_rsNotes = imw_fetch_array($resourceProgressNotes))
	{
		$ProgressNotesTime = $row_rsNotes['tTime'];
		//CODE TO SET $ProgressNotesTime 
			if($ProgressNotesTime=="00:00:00" || $ProgressNotesTime=="") {
				
			//$ProgressNotesTime=date("h:i A");
			$ProgressNotesTime=$objManageData->getTmFormat(date("H:i:s"));
			}else {
				$ProgressNotesTime=$ProgressNotesTime;
			}
			
			$time_split_ProgressNotesTime = explode(":",$ProgressNotesTime);
			if($time_split_ProgressNotesTime[0]=='24') { //to correct previously saved records
				$ProgressNotesTime = "12".":".$time_split_ProgressNotesTime[1].":".$time_split_ProgressNotesTime[2];
			}
			//$time = date('h:i A',strtotime($ProgressNotesTime));
			$time = $objManageData->getTmFormat($ProgressNotesTime);

			/*	
			if($time_split_ProgressNotesTime[0]>=12) {
				$am_pm = "PM";
			}else {
				$am_pm = "AM";
			}
			if($time_split_ProgressNotesTime[0]>=13) {
				$time_split_ProgressNotesTime[0] = $time_split_ProgressNotesTime[0]-12;
				if(strlen($time_split_ProgressNotesTime[0]) == 1) {
					$time_split_ProgressNotesTime[0] = "0".$time_split_ProgressNotesTime[0];
				}
			}else {
				//DO NOTHNING
			}
			$time = $time_split_ProgressNotesTime[0].":".$time_split_ProgressNotesTime[1]." ".$am_pm;
			*/
		$datestring= $row_rsNotes['dtDateTime']; 
		$d=explode("-",$datestring);
		$date =  $d[1]."/".$d[2]."/".$d[0];
			
		$table_main4.='
			<tr style=" height:100px;">
				<td style="margin-top:50px; width:150px;" align="left" valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>'.$date.'</strong></font></td>
				<td style="width:150px;"   align="left" valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>'.$time.'</strong></font></td>
				<td style="width:150px;"   align="left" valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>'.$row_rsNotes['user_type'].'</strong></font></td>
				<td style="width:150px;"   align="left" valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>'.$row_rsNotes['fname']." ".$row_rsNotes['lname'].'</strong></font></td>
				<td align="left" valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>&nbsp;</strong></font></td>
			</tr>
			<tr>
				<td colspan="5" align="left">'.htmlentities($row_rsNotes['txtNote']).'</td>
			</tr>
			<tr>
				<td colspan="5" align="left" height="10">&nbsp;</td>
			</tr>
			';
	}
	$table_main4.='</table></page>';	
	$table.=$table_main4;
}

$scanItemQry = "SELECT sut.*,sd.document_name as scanCategoryName FROM scan_upload_tbl sut 
				INNER JOIN scan_documents sd ON (sd.document_id = sut.document_id)
				WHERE sut.confirmation_id = '".$pConfId."' ORDER BY sd.document_name, sut.image_type";
$scanItemRes = imw_query($scanItemQry) or die(imw_error());
if(imw_num_rows($scanItemRes)>0) {
	$tableScan='';
	$lable="Scanned/Uploaded Items";
	
	while($scanItemRow 	= imw_fetch_array($scanItemRes)) {
		$img_content 	= $scanItemRow["img_content"];
		$image_type 	= $scanItemRow["image_type"];
		$document_name 	= $scanItemRow["document_name"];
		$scanCategoryName 	= $scanItemRow["scanCategoryName"];
		$dcNme = str_ireplace(" ","-",$document_name);
		$itemName = $dcNme.date('d_m_y_h_i_s');
		$filePath = $scanItemRow["pdfFilePath"];
		$rootFilePath = 'admin/'.$filePath;
		
		$fileName = "";
		if($img_content) {
			$fileName = 'html2pdfnew/'.$itemName.'.jpg';
			$bakImgResourceScanItem = imagecreatefromstring($img_content);
			imagejpeg($bakImgResourceScanItem,$fileName);
		}
		
		if( $filePath && file_exists($rootFilePath) ) {
			$ext = pathinfo($rootFilePath, PATHINFO_EXTENSION);
			$ext = strtolower($ext);
			if( $ext <> 'pdf' && $ext <> 'png') {
				$fileName = $rootFilePath;
			}
		}
				
		if($fileName && file_exists($fileName)){
				$priImageSize = getimagesize($fileName);
				if($priImageSize[0] > 395 && $priImageSize[1] < 840){
					$newSize = $objManageData->imageResize(680,400,500);						
					$priImageSize[0] = 500;
				}					
				elseif($priImageSize[1] > 840){
					$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
					$priImageSize[1] = 600;
				}
				else{					
					$newSize = $priImageSize[3];
				}							
				
				$tableScan.='<page>'."\n";
				$tableScan.='<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">';				
				$tableScan.='	<tr>
									<td align="center" height="7"><strong>'.$scanCategoryName.' - '.$document_name.'</strong></td>
								</tr>';
				
				$tableScan.='<tr>
								<td align="center" ><img src="../'.$fileName.'" '.$newSize.'></td>
							 </tr>';
				$tableScan.='</table></page>';	
				
			}
		
	}
	
	$table.=$tableScan;

}

//START CODE TO DECODE THE SRC OF IMAGE
if(stristr($table,";base64,")) {
	$image_type				= "png";
	$image_parts 			= explode(";base64,", $table);
	$image_type_aux 		= explode("image/", $image_parts[0]);
	if($image_type_aux[1]) {
		$image_type 		= $image_type_aux[1];
	}
	list($image_parts_new) 	= explode('"',$image_parts[1]);
	$image_base64 			= base64_decode($image_parts_new);
	$fileConsent 			= $rootServerPath."/".$surgeryCenterDirectoryName."/html2pdfnew/consent_image_".$pConfId.".".$image_type;
	file_put_contents($fileConsent, $image_base64);
	$fileConsent = str_ireplace(".".$image_type,".jpg",$fileConsent);
	$bakImgResource = imagecreatefromstring($image_base64);
	imagejpeg($bakImgResource,$fileConsent);
	$fileConsentImgSrc = str_ireplace($rootServerPath."/".$surgeryCenterDirectoryName."/html2pdfnew/","../html2pdfnew/",$fileConsent);
	$table = str_ireplace(trim("data:image/".$image_type.";base64,".$image_parts_new),trim($fileConsentImgSrc),$table);
}
//END CODE TO DECODE THE SRC OF IMAGE


$table=str_ireplace('/'.$surgeryCenterDirectoryName.'/new_html2pdf/','',$table);
$table=str_ireplace('<p style="text-align: left"></p>','',$table);
$table=str_ireplace('<p style="text-align: left">  </p>','',$table);
$table=str_ireplace('<p style="text-align: left"> </p>','',$table);
$table=str_ireplace('<p>&nbsp;</p>','',$table);
$table=str_ireplace('<p> </p>','',$table);
$table=str_ireplace('<p><strong><span style="font-size:12pt"> </span></strong></p>','',$table);
$table=str_ireplace('<p><strong><span style="font-size:12pt">&nbsp;</span></strong></p>','',$table);
$matchesArr = array();
preg_match_all('@font-family(\s*):(.*?)(\s?)("|;|$)@i', $table, $matchesArr);
if (count($matchesArr[2])>0) {
	foreach($matchesArr[0] as $matchesKey=> $matches ) {
		$matchesVal=str_ireplace('"','',$matches);
		$table=str_ireplace($matchesVal,'',$table);	
	}
}
//$table = preg_replace('/font-family.+?;/', "", $table);

$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$table);
fclose($fileOpen);
?>
<script language="javascript">
	function submitfn(){
		document.printFrm.submit();
	}
</script>

<form name="printFrm"  action="new_html2pdf/createPdf.php?op=p&onePage=false&merge_pdf=1&pConfId=<?php echo $pConfId; ?>" method="post" >
</form>		

<?php
/*if(trim($grid_image_path)) {//CODE TO PRINT LOCAL ANES GRAPH
?>
	<iframe src="local_anes_record.php?patient_id=<?php echo $Consent_patientConfirm_tblRow["patientId"];?>&pConfId=<?php echo $_REQUEST['pConfId'];?>&printAnesthesiaGridFrame=yes" style="height:0px; width:0px;"></iframe>
<?php		
}else {*/
?>
<script type="text/javascript">
	submitfn();
</script>
<?php
//}
?>