<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

session_start();
$purgeDir 		= "admin/pdfFiles/purge";
$purgeDirPath 	= realpath(dirname(__FILE__)."/".$purgeDir);
if(!is_dir($purgeDir)) {
	mkdir($purgeDir);
}
if(!is_dir($purgeDirPath)) {
	mkdir($purgeDirPath);
}

include_once("common/conDb.php");
include("common_functions.php");
//include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
// allergies_status_reviewed(table field t be added)
//$patient_id = $_REQUEST['patient_id'];
//$ascId = $_REQUEST['ascId'];
$pConfId = $_REQUEST['pConfId'];
include_once("new_header_print.php");

//START GET VOCABULARY OF ASC
$ascInfoArr = $objManageData->getASCInfo($_SESSION["facility"]);
//END GET VOCABULARY OF ASC

//GETTING PATIENT CONFIRMATION DETAILS
	$confirmationDetails = $objManageData->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
	if(count($confirmationDetails)>0){
		@extract($confirmationDetails);
		if(!$patient_id){
			$patient_id = $patientId;
		}
	}
	//START GET ARRIVAL TIME
	$stubWaitingDetailArr = $objManageData->getStubWaitingDetail($pConfId,$dos)	;
	$arrivalTime = ($stubWaitingDetailArr[0]) ? $stubWaitingDetailArr[0] : '';	
	//END GET ARRIVAL TIME
	
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
			$instDetails = $objManageData->getRowRecord("patient_instruction_sheet", "patient_confirmation_id", $pConfId, "", "", " *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat, date_format(signWitness1DateTime,'%m-%d-%Y %h:%i %p') as signWitness1DateTimeFormat");
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
				$WitnessInstructionNameShow 	= $instDetails->signWitness1LastName.", ".$instDetails->signWitness1FirstName." ".$instDetails->signWitness1MiddleName;
				$signWitness1Status 		= $instDetails->signWitness1Status;
					
			
				if($signSurgeon1Activate=='yes' || $signNurseActivate=='yes') {
					$instructionData.='
						<table style="width:700px;"  cellpadding="0" cellspacing="0">
							<tr valign="top">';
							if($signSurgeon1Activate=='yes') {
								$instructionData.='<td style="width:350px;" class="fm">';
								if($signSurgeon1InstructionId) { 
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
							
								$instructionData.='</td>';
							}
							if($signNurseActivate=='yes') {
								$instructionData.='<td style="width:350px;" class="fm">';
								if($signNurseInstructionId) { 
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
								$instructionData.='</td>';
							}
						$instructionData.='	</tr></table>';
				}
			}
			//	CODE FOR ANESTHESIA AND WITNESS SIGN
			
			if($signAnesthesia1Activate=='yes' || $signWitness1Activate=='yes') {
				$instructionData.='<table border="0" cellpadding="0" cellspacing="0"><tr>';
				if($signWitness1Activate=='yes' ) {
					$instructionData.='<td nowrap align="left" class="text_10 fm" valign="middle" style="cursor:hand;" >';
					if( $signWitness1Id ) { 
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
					$instructionData.='</td>';
				}
				$instructionData.='</tr></table>';
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
	if(!$patient_id) {
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
		$diagnosisQry=imw_query("select preOpDiagnosis , postOpDiagnosis from operatingroomrecords where patient_id='".$patient_id."' and confirmation_id='".$pConfId."'");
		$diagnosisRes=imw_fetch_array($diagnosisQry);	
		$preopdiagnosis= $diagnosisRes["preOpDiagnosis"];
		$postopdiagnosis= $diagnosisRes["postOpDiagnosis"];
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
	$instructionData = str_ireplace('/'.$surgeryCenterDirectoryName.'/new_html2pdf/',"",$instructionData);
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
	$instructionData = str_ireplace( '/surgerycenter/',$_SERVER['DOCUMENT_ROOT'].'/surgerycenter/', $instructionData);
//GETTING SITE DETAILS

	
?>
<body  style="background-color:#FFFFFF; ">	
<?php

$table_main='';			
$table_main.=$head_table."\n";	
$labelHeading = "Instruction Sheet";
if($_REQUEST["hiddPurgeResetStatusId"]=="Yes") {
	$labelHeading = "<span style='color:#FF0000;background:#FFFFFF;'>Purged Instruction Sheet</span>";
}
$table_main.='<table style="width:740px;" cellpadding="0" cellspacing="0">
				<tr>	
					<td style="width:740px;" class="fheader fm">'.$labelHeading.'</td>
				</tr>';
			
			if($instructionData!=""){
				$table_main.='
						<tr>
							<td style="width:740px;" class="bgcolor bdrbtm cbold fm">Patient Instruction Sheet</td>
						</tr>
						<tr>
							<td style="width:740px;" '.((LOCAL_SERVER == 'yourservername') ? '' : 'class="fl"').'>'.strip_tags($instructionData,'<img> <p> <br> <b> <p> <div> <table> <tbody> <tr> <td> <strong> <u> <span> '.$strip_p).'</td>
						</tr>';
			}
$table_main.='</table>';	
	
$table_main=str_ireplace('<p style="text-align: left"></p>','',$table_main);
$table_main=str_ireplace('<p style="text-align: left">  </p>','',$table_main);
$table_main=str_ireplace('<p style="text-align: left"> </p>','',$table_main);
$table_main=str_ireplace('<p style="text-align: left">&nbsp;</p>','',$table_main);
$table_main=str_ireplace('<p>&nbsp;</p>','',$table_main);
$table_main=str_ireplace('<p> </p>','',$table_main);
$table_main=str_ireplace('<p style="text-align:center">&nbsp;</p>','',$table_main);
$table_main=str_ireplace('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;','',$table_main);   
$table_main = str_ireplace('/'.$surgeryCenterDirectoryName.'/new_html2pdf/','',$table_main);
   
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$table_main);
fclose($fileOpen);

if($_REQUEST["hiddPurgeResetStatusId"]=="Yes") {
	include("new_html2pdf/html2pdf.class.php");
	$op 		= "p";
	$content  	= $table_main;
	$content 	= str_ireplace("../SigPlus_images/","SigPlus_images/",$content);
	$content 	= str_ireplace("../html2pdf/","html2pdf/",$content);
	$content 	= str_ireplace("../html2pdfnew/","html2pdfnew/",$content);
	$content 	= str_ireplace("../new_html2pdf/","new_html2pdf/",$content);
	$html2pdf 	= new HTML2PDF($op,'A4','en');
	$html2pdf->setTestTdInOnePage(false);file_put_contents("new_html2pdf/a.txt",'content = '.$content);
	$html2pdf->WriteHTML($content, isset($_GET['vuehtml']));file_put_contents("new_html2pdf/b.txt",'content = '.$content);
	
	//$html2pdf->Output('new_html2pdf/Purged_Discharge_Instruction.pdf','F');
	
	$purgeDocName = "purged_instruction_sheet_".$_REQUEST["pConfId"]."_".date("ymdhis").".pdf";
	$purgePath = $purgeDir."/".$purgeDocName;
	$html2pdf->Output($purgePath,"F");
	$purgeDirSave 	= "pdfFiles/purge/".$purgeDocName;
	if(file_exists($purgePath)) {
		//ADD IT IN PT INFO FOLDER
		$chkScnQry = "SELECT document_id FROM scan_documents WHERE confirmation_id = '".$_REQUEST["pConfId"]."' AND document_name = 'Pt. Info' ORDER BY document_id DESC LIMIT 0,1 ";
		$chkScnRes = imw_query($chkScnQry);
		if(imw_num_rows($chkScnRes)>0) {
			$chkScnRow = imw_fetch_assoc($chkScnRes);
			$scn_document_id = $chkScnRow["document_id"];		
			
		}else {
			$insQry = "INSERT INTO scan_documents SET document_name = 'Pt. Info', patient_id = '".$patient_id."', confirmation_id = '".$_REQUEST["pConfId"]."', dosOfScan = '".$dos."' ";
			$insRes = imw_query($insQry);
			$scn_document_id = imw_insert_id();
		}
		if($scn_document_id) {
			unset($arrayRecord);
			$arrayRecord["confirmation_id"] 			= $_REQUEST["pConfId"];
			$arrayRecord["patient_id"] 	 				= $patient_id;
			$arrayRecord["image_type"] 	 				= "application/pdf";
			$arrayRecord["document_name"] 				= $purgeDocName;
			$arrayRecord["document_id"] 				= $scn_document_id;
			$arrayRecord["pdfFilePath"] 				= $purgeDirSave;
			$arrayRecord["scan_upload_save_date_time"] 	= date("Y-m-d H:i:s");
			$scan_upload_id = $objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
		}
	}
	die();
}
if($instSheetFormStatus=='completed' || $instSheetFormStatus=='not completed'){
?>
<script language="javascript">
	function submitfn(){
		document.printFrm.submit();
	}
</script>
<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
	</tr>
</table>

<body>
<form name="printFrm"  action="new_html2pdf/createPdf.php?op=p" method="post">
</form>		
<script type="text/javascript">
	submitfn();
</script>
</body>
<?php
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>	
