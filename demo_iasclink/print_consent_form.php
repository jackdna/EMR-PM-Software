<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
	session_start();
	include_once("common/conDb.php");
	//include("common_functions.php");
	include_once("common/commonFunctions.php"); 
	//include("common/linkfile.php");
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
	$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
	
?>
<style>
	body { text-align:center; margin: 0px; background: #ECF1EA; width:100%; font:normal 11px Verdana, Arial, sans-serif;color:#000}
</style>
<script>
	if(top.document.getElementById("anchorShow")) {
		top.document.getElementById("anchorShow").style.display = 'none';
	}
	if(top.document.getElementById("deleteSelected")) {
		top.document.getElementById("deleteSelected").style.display = 'block';
	}
	if(top.document.getElementById("PrintBtn")) {
		top.document.getElementById("PrintBtn").style.display = 'none';
	}
	if(top.document.getElementById("multiUploadImgBtn")) {
		top.document.getElementById("multiUploadImgBtn").style.display = 'none';
	}
</script>
	
<body >
<?php	
	$get_http_path=$_REQUEST['get_http_path'];
	//include "header_print.php";
	//$lable="Patient Consent";
	
	
	/*
	$pConfId = $_REQUEST['pConfId'];
	$patient_id = $_REQUEST['patient_id'];
	if(!$patient_id) {
		$patient_id = $_SESSION['patient_id'];
	}	
	
	$consentMultipleId  =$_REQUEST['consentMultipleId'];
	$consentMultipleAutoIncrId = $_REQUEST['consentMultipleAutoIncrId'];
	*/
	$patient_id=$_REQUEST['patient_id'];
	$intPatientWaitingId=$_REQUEST['intPatientWaitingId'];
	$consentMultipleId=$_REQUEST['intConsentTemplateId'];
	
	if($consentMultipleAutoIncrId){
		$consentAutoIncrIdQry = " AND surgery_consent_id='".$consentMultipleAutoIncrId."'";
	}	
	
//START GET PATIENT PERSONAL DETAIL
$Consent_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
$Consent_patientName_tblRes = imw_query($Consent_patientName_tblQry) or die(imw_error());
$Consent_patientName_tblRow = imw_fetch_array($Consent_patientName_tblRes);
$Consent_patientName = $Consent_patientName_tblRow["patient_lname"].", ".$Consent_patientName_tblRow["patient_fname"]." ".$Consent_patientName_tblRow["patient_mname"];
$Consent_patientNameDobTemp = $Consent_patientName_tblRow["date_of_birth"];
$Consent_patientNameDob_split = explode("-",$Consent_patientNameDobTemp);
$Consent_patientNameDob = $Consent_patientNameDob_split[1]."-".$Consent_patientNameDob_split[2]."-".$Consent_patientNameDob_split[0];
//END GET PATIENT PERSONAL DETAIL

//START GET PATIENT OTHER DETAIL
	$Consent_patientConfirm_tblQry = "SELECT * FROM `patient_in_waiting_tbl` WHERE `patient_in_waiting_id` = '".$intPatientWaitingId."'";
	$Consent_patientConfirm_tblRes = imw_query($Consent_patientConfirm_tblQry) or die(imw_error());
	if(imw_num_rows($Consent_patientConfirm_tblRes)>0) {
		$Consent_patientConfirm_tblRow = imw_fetch_array($Consent_patientConfirm_tblRes);
		$Consent_patientConfirmDosTemp = $Consent_patientConfirm_tblRow["dos"];
		$Consent_patientConfirmDos_split = explode("-",$Consent_patientConfirmDosTemp);
		$Consent_patientConfirmDos = $Consent_patientConfirmDos_split[1]."-".$Consent_patientConfirmDos_split[2]."-".$Consent_patientConfirmDos_split[0];
	
		
		$Consent_surgeon_fname = $Consent_patientConfirm_tblRow["surgeon_fname"];
		$Consent_surgeon_mname = $Consent_patientConfirm_tblRow["surgeon_mname"];
		$Consent_surgeon_lname = $Consent_patientConfirm_tblRow["surgeon_lname"];
		
		$Consent_patientConfirmSurgeon = $Consent_surgeon_lname.", ".$Consent_surgeon_fname." ".$Consent_surgeon_mname;
		$Consent_patientConfirmSite = $Consent_patientConfirm_tblRow["site"];
		$Consent_patientConfirmPrimProc = $Consent_patientConfirm_tblRow["patient_primary_procedure"];
		$Consent_patientConfirmSecProc 	= $Consent_patientConfirm_tblRow["patient_secondary_procedure"];
	
	}
//END GET PATIENT OTHER DETAIL 
	
	//GET PATIENT DETAIL
	/*
	$Consent_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
	$Consent_patientName_tblRes = imw_query($Consent_patientName_tblQry) or die(imw_error());
	$Consent_patientName_tblRow = imw_fetch_array($Consent_patientName_tblRes);
	$Consent_patientName = $Consent_patientName_tblRow["patient_lname"].", ".$Consent_patientName_tblRow["patient_fname"]." ".$Consent_patientName_tblRow["patient_mname"];
	
	
	$Consent_patientNameDobTemp = $Consent_patientName_tblRow["date_of_birth"];
		$Consent_patientNameDob_split = explode("-",$Consent_patientNameDobTemp);
		$Consent_patientNameDob = $Consent_patientNameDob_split[1]."-".$Consent_patientNameDob_split[2]."-".$Consent_patientNameDob_split[0];
	

	
	$Consent_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$Consent_patientConfirm_tblRes = imw_query($Consent_patientConfirm_tblQry) or die(imw_error());
	$Consent_patientConfirm_tblRow = imw_fetch_array($Consent_patientConfirm_tblRes);
	$finalizeStatus = $Consent_patientConfirm_tblRow["finalize_status"];
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
	*/
	//VIEW RECORD FROM DATABASE 
	$ViewConsentSurgeryQry = "select * from `iolink_consent_filled_form` where  fldPatientWaitingId = '".$intPatientWaitingId."' AND consent_template_id='".$consentMultipleId."' ".$consentAutoIncrIdQry." AND consent_template_id!='0'";
	$ViewConsentSurgeryRes = imw_query($ViewConsentSurgeryQry) or die(imw_error()); 
	$ViewConsentSurgeryNumRow = imw_num_rows($ViewConsentSurgeryRes);
	$ViewConsentSurgeryRow = imw_fetch_array($ViewConsentSurgeryRes); 
	
	$consentSurgery_patient_sign = $ViewConsentSurgeryRow["surgery_consent_sign"];
	$surgery_consent_data = stripslashes($ViewConsentSurgeryRow["surgery_consent_data"]);
	$lable = $ViewConsentSurgeryRow["surgery_consent_alias"];
	$form_status = $ViewConsentSurgeryRow["form_status"];
	$consentPurgeStatus = $ViewConsentSurgeryRow["consent_purge_status"];
	$saveLink = $saveLink."&form_status=".$form_status;
	
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE 
	if(trim($surgery_consent_data)=="") {
		/*
		$ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = '".$consentMultipleId."'";
		$ViewConsentTemplateRes = imw_query($ViewConsentTemplateQry) or die(imw_error()); 
		$ViewConsentTemplateNumRow = imw_num_rows($ViewConsentTemplateRes);
		$ViewConsentTemplateRow = imw_fetch_array($ViewConsentTemplateRes);  
			
		$surgery_consent_data = stripslashes($ViewConsentTemplateRow["consent_data"]);
		*/
		echo $surgery_consent_data = "<center>Please verify/save this form before print</center>";
		exit();
	}
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE		

//image for signature
$preid=$intPatientWaitingId;
$pertable = 'surgery_consent_form';
$preidName = 'confirmation_id';
$predocSign = 'surgery_consent_sign';
$qry = "select surgery_consent_sign from iolink_consent_filled_form where fldPatientWaitingId = $intPatientWaitingId and consent_template_id = '".$consentMultipleId."'";
$pixleRes = imw_query($qry);
list($surgery_consent_sign) = imw_fetch_array($pixleRes);
require_once("imgGd.php");
drawOnImage($surgery_consent_sign,$imgName,'123.jpg');
/*******End of signature*****/

	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
		$surgery_consent_data= str_ireplace("{PATIENT FIRST NAME}","<b>".$Consent_patientName_tblRow["patient_fname"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{MIDDLE INITIAL}","<b>".$Consent_patientName_tblRow["patient_mname"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{LAST NAME}","<b>".$Consent_patientName_tblRow["patient_lname"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{DOB}","<b>".$Consent_patientNameDob."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{SURGEON NAME}","<b>".$Consent_patientConfirmSurgeon."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{SITE}","<b>".$Consent_patientConfirmSite."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{PROCEDURE}","<b>".$Consent_patientConfirmPrimProc."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$surgery_consent_data);
		//$surgery_consent_data = str_ireplace('{SIGNATURE}',"",$surgery_consent_data);
		
		$surgery_consent_data= str_ireplace("{ASC NAME}",$_SESSION['iolink_loginUserFacilityName'],$surgery_consent_data);

		$surgery_consent_data = str_ireplace('{TEXTBOX_XSMALL}',"<input type='text' name='xsmall' size='1' maxlength='1'>",$surgery_consent_data);
		$surgery_consent_data = str_ireplace('{TEXTBOX_SMALL}',"<input type='text' name='small' size='30' maxlength='30'>",$surgery_consent_data);
		$surgery_consent_data = str_ireplace('{TEXTBOX_MEDIUM}',"<input type='text' name='medium' size='60' maxlength='60'>",$surgery_consent_data);
		$surgery_consent_data = str_ireplace('{TEXTBOX_LARGE}',"<textarea name='large' cols='100' rows='2'></textarea>",$surgery_consent_data);
		$surgery_consent_data = str_replace('SigPlus_images/',"../SigPlus_images/",$surgery_consent_data);
		$surgery_consent_data = str_replace('html2pdfnew/sign_',"../html2pdfnew/sign_",$surgery_consent_data);
		
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
			$surgery_consent_data = str_ireplace('<input type="text"  name="'.$verySmallTextbox.'" value="','',$surgery_consent_data);
			$surgery_consent_data = str_ireplace('" size="1"  maxlength="1">','',$surgery_consent_data);
		}
		//REPLACE MULTIPLE VERY SMALL TEXTBOX
		
		//REPLACE MULTIPLE SMALL TEXTBOX
		$smallTextBoxExplode = explode('name="small',$surgery_consent_data);
		for($j=1;$j<count($smallTextBoxExplode);$j++) {
			
			$smallTextbox = 'small'.$j;
			$surgery_consent_data = str_ireplace('<input type="text"  name="'.$smallTextbox.'" value="','',$surgery_consent_data);
			$surgery_consent_data = str_ireplace('" size="30"  maxlength="30">','',$surgery_consent_data);
		}
		//REPLACE MULTIPLE SMALL TEXTBOX
		
		//REPLACE MULTIPLE MEDIUM TEXTBOX
		$mediumTextBoxExplode = explode('name="medium',$surgery_consent_data);
		for($k=1;$k<count($mediumTextBoxExplode);$k++) {
			
			$mediumTextbox = 'medium'.$k;
			$surgery_consent_data = str_ireplace('<input type="text"  name="'.$mediumTextbox.'" value="','',$surgery_consent_data);
			$surgery_consent_data = str_ireplace('" size="60"  maxlength="60">','',$surgery_consent_data);
		}
		//REPLACE MULTIPLE MEDIUM TEXTBOX

		//END SET TEXT BOX VALUES
	//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
//END VIEW RECORD FROM DATABASE
	
	//START CODE OF SIGNATURE (FROM IMEDIC TO IOLINK) BY RAVI
	$qry = "select signature_image_path,signature_count from iolink_consent_form_signature 
			where patient_id = '$patient_id' and patient_in_waiting_id = '$intPatientWaitingId'
			and consent_template_id  = '$consentMultipleId' order by signature_count";
	$rsQry = imw_query($qry);
	$numRowQry = imw_num_rows($rsQry);
	if($numRowQry > 0){
		$sig_con = array();
		$s=0;
		while($rsRow = imw_fetch_array($rsQry)) {			
			$sig_con[$s] = $rsRow['signature_image_path'];
			$signature_count[$s] = $rsRow['signature_count'];
			$s++;
			
		}
		for($ps=0;$ps<count($sig_con);$ps++){
			$row_arr = explode('{START APPLET ROW}',$surgery_consent_data);
			$sig_arr = explode('{SIGNATURE}',$row_arr[0]);	
			//print_r($sig_arr);
			//exit;			
			$sig_data = '';
			$ds=0;
			$coun=0;
			for($s=1;$s<count($sig_arr);$s++){
				if($s==$signature_count[$ds]){
					$postData = $sig_con[$coun];
					//echo $postData;					
					$path1 = explode("\\",$postData);					
					//echo ($path1[6]);
					//exit;
						if(isset($path1[6]) && !empty($path1[6])){
							$sig_data = '<table>
							<tr>
								<td>
									<img src="'.$path1[6].'" height="10%" width="30%">
								</td>
							</tr></table>';
							$str_data = $sig_arr[$s];
							$sig_arr[$s] = $sig_data;
							$sig_arr[$s] .= $str_data;
							$hiddenFields[] = true;
							
						}
						$coun++;
					$ds++;
				}
			}
			$surgery_consent_data = implode(' ',$sig_arr);
			$content_row = '';
			for($ro=1;$ro<count($row_arr);$ro++){
				if($row_arr[$ro]){
					$sig_arr1 = explode('{SIGNATURE}',$row_arr[$ro]);
					$td_sign = '';
					for($t=0;$t<count($sig_arr1)-1;$t++,$ds++){
						$sig_arr1[$t] = str_ireplace('&nbsp;','',$sig_arr1[$t]);
						$td_sign .= '
							<td align="left">
								<table border="0">
									<tr><td>'.$sig_arr1[$t].'</td></tr>
									<tr>
										<td style="border:solid 1px" bordercolor="#FF9900">
											{SIGNATURE}
										</td>
									</tr>
								</table>
							</td>	
						';
						$s++;
						$hiddenFields[] = true;
					}
					$content_row .= '
						<table width="145" border="1" align="center">
							<tr>
								'.$td_sign.'						
							</tr>
						</table>
					';
				}
			}
			$jh = 1;
			$surgery_consent_data .= $content_row;
		}
	}
	//END CODE OF SIGNATURE (FROM IMEDIC TO IOLINK) BY RAVI
	$table.=$head_table."\n";
	
					$table.='<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">';
					
					if($consentPurgeStatus=="true"){
						$table.='<tr><td  height="7" align="center"><font color="#FF0000" ><strong>CONSENT FORM PURGED</strong></font></td></tr>';
					}else{
						$table.='<tr><td  height="7">&nbsp;</td></tr>';
					}

				   $table.='<tr>
								<td  align="center"><strong>'.$lable.'</strong></td>
						   </tr>
						   <tr><td  height="7">&nbsp;</td></tr>
						   <tr>
							<td></td>
						   </tr>
					   </table>';
	
	//START CODE FOR SURGEON AND NURSE SIGN
	
	$signSurgeon1Activate = $ViewConsentSurgeryRow["signSurgeon1Activate"];
	$signSurgeon1ConsentId = $ViewConsentSurgeryRow["signSurgeon1Id"];
	$Surgeon1ConsentName = $ViewConsentSurgeryRow["signSurgeon1LastName"].", ".$ViewConsentSurgeryRow["signSurgeon1FirstName"]." ".$ViewConsentSurgeryRow["signSurgeon1MiddleName"];
	$Surgeon1ConsentSignOnFileStatus = $ViewConsentSurgeryRow["signSurgeon1Status"];
	$signSurgeon1SaveDateTime = date('m-d-Y h:i A',strtotime($ViewConsentSurgeryRow["signSurgeon1DateTime"]));
	
	$signNurseActivate = $ViewConsentSurgeryRow["signNurseActivate"];
	$signNurseConsentId = $ViewConsentSurgeryRow["signNurseId"];
	$NurseConsentNameShow = $ViewConsentSurgeryRow["signNurseLastName"].", ".$ViewConsentSurgeryRow["signNurseFirstName"]." ".$ViewConsentSurgeryRow["signNurseMiddleName"];
	$NurseConsentSignOnFileStatus = $ViewConsentSurgeryRow["signNurseStatus"];
	$signNurse1SaveDateTime = date('m-d-Y h:i A',strtotime($ViewConsentSurgeryRow["signNurseDateTime"]));

	if($signSurgeon1Activate=='yes' || $signNurseActivate=='yes') {
		$surgery_consent_data.='
			<table border="0" cellpadding="0" cellspacing="0" width="700">
				<tr>
					<td nowrap align="left" class="text_10" valign="middle" style="cursor:pointer;" width="450" >
			';
				if($signSurgeon1Activate=='yes' && $signSurgeon1ConsentId) { 
					$surgery_consent_data.='
									<b>Surgeon:</b> Dr '.$Surgeon1ConsentName.'
									<br><b>Signature On File :&nbsp;</b>'.$Surgeon1ConsentSignOnFileStatus.'
									<br><b>Signature Date :&nbsp;</b>'.$signSurgeon1SaveDateTime.'
					';
				}
				$surgery_consent_data.='</td>
				<td nowrap align="left" class="text_10" valign="middle" style="cursor:pointer;" width="250" >';
				if($signNurseActivate=='yes' && $signNurseConsentId) { 
					$surgery_consent_data.='
									<b>Nurse:</b> '.$NurseConsentNameShow.'
									<br><b>Signature On File :&nbsp;</b>'.$NurseConsentSignOnFileStatus.'
									<br><b>Signature Date :&nbsp;</b>'.$signNurse1SaveDateTime.'
					';
				}
		$surgery_consent_data.='
					</td>
				</tr>
				<tr height="25"><td colspan="2">&nbsp;</td></tr>
			</table>';
	}
		 
	//END CODE FOR SURGEON AND NURSE SIGN

	$signAnesthesia1Activate 	= $ViewConsentSurgeryRow["signAnesthesia1Activate"];
	$signAnesthesia1Id 			= $ViewConsentSurgeryRow["signAnesthesia1Id"];
	$AnesthesiaConsentNameShow  = $ViewConsentSurgeryRow["signAnesthesia1LastName"].", ".$ViewConsentSurgeryRow["signAnesthesia1FirstName"]." ".$ViewConsentSurgeryRow["signAnesthesia1MiddleName"];
	$signAnesthesia1Status 		= $ViewConsentSurgeryRow["signAnesthesia1Status"];
	$signAnesthesia1SaveDateTime = date('m-d-Y h:i A',strtotime($ViewConsentSurgeryRow["signAnesthesia1DateTime"]));
	
	$signWitness1Activate 		= $ViewConsentSurgeryRow["signWitness1Activate"];
	$signWitness1Id 			= $ViewConsentSurgeryRow["signWitness1Id"];
	$WitnessConsentNameShow 	= $ViewConsentSurgeryRow["signWitness1LastName"].", ".$ViewConsentSurgeryRow["signWitness1FirstName"]." ".$ViewConsentSurgeryRow["signWitness1MiddleName"];
	$signWitness1Status 		= $ViewConsentSurgeryRow["signWitness1Status"];
	$signWitness1SaveDateTime = date('m-d-Y h:i A',strtotime($ViewConsentSurgeryRow["signWitness1DateTime"]));
//	CODE FOR ANESTHESIA AND WITNESS SIGN
		
	if($signAnesthesia1Activate=='yes' || $signWitness1Activate=='yes') {
		$surgery_consent_data.='
			<table border="0" cellpadding="0" cellspacing="0" width="700">
				<tr>
					<td nowrap align="left" class="text_10" valign="middle" style="cursor:pointer;" width="450" >
			';
				if($signAnesthesia1Activate=='yes' && $signAnesthesia1Id) { 
					$Anesthesia1PreFix = 'Dr';
					$Anesthesia1SubType = getUserSubTypeFun($signAnesthesia1Id); //FROM common/commonFunctions.php
					if($Anesthesia1SubType=='CRNA') {$Anesthesia1PreFix = ''; }
					$surgery_consent_data.='
									<b>Anesthesiologist:</b> '.$Anesthesia1PreFix.' '.$AnesthesiaConsentNameShow.'
									<br><b>Signature On File :&nbsp;</b>'.$signAnesthesia1Status.'
									<br><b>Signature Date :&nbsp;</b>'.$signAnesthesia1SaveDateTime.'
					';
				}
				$surgery_consent_data.='</td>
				<td nowrap align="left" class="text_10" valign="middle" style="cursor:pointer;" width="250" >';
			
				if($signWitness1Activate=='yes' && $signWitness1Id) { 
					$surgery_consent_data.='
									<b>Witness:</b> '.$WitnessConsentNameShow.'
									<br><b>Signature On File :&nbsp;</b>'.$signWitness1Status.'
									<br><b>Signature Date :&nbsp;</b>'.$signWitness1SaveDateTime.'
					';
				}
		$surgery_consent_data.='
					</td>
				</tr>
				<tr height="25"><td colspan="2">&nbsp;</td></tr>
			</table>';
	}
	//END CODE FOR ANESTHESIA AND WITNESS SIGN
	
	//START CODE TO DECODE THE SRC OF IMAGE
	if(stristr($surgery_consent_data,";base64,")) {
		$image_type				= "png";
		$image_parts 			= explode(";base64,", $surgery_consent_data);
		$image_type_aux 		= explode("image/", $image_parts[0]);
		if($image_type_aux[1]) {
			$image_type 		= $image_type_aux[1];
		}
		list($image_parts_new) 	= explode('"',$image_parts[1]);
		$image_base64 			= base64_decode($image_parts_new);
		$fileConsent 			= $rootServerPath."/".$iolinkDirectoryName."/html2pdfnew/iolink_consent_image.".$image_type;
		file_put_contents($fileConsent, $image_base64);
		$fileConsent = str_ireplace(".".$image_type,".jpg",$fileConsent);
		$bakImgResource = imagecreatefromstring($image_base64);
		imagejpeg($bakImgResource,$fileConsent);
		$fileConsentImgSrc = str_ireplace($rootServerPath."/".$iolinkDirectoryName."/html2pdfnew/","../html2pdfnew/",$fileConsent);
		$surgery_consent_data = str_ireplace(trim("data:image/".$image_type.";base64,".$image_parts_new),trim($fileConsentImgSrc),$surgery_consent_data);
	}
	//END CODE TO DECODE THE SRC OF IMAGE
	
	$table.= strip_tags($surgery_consent_data,'<img> <br> <u> <b> <p> <div> <table> <tbody> <tr> <td> ');
	$table = str_ireplace('&nbsp;<br />','',$table);
	$table = str_ireplace('<p>&nbsp;</p>','',$table);
	
	$table.='<table cellpadding="0" cellspacing="0" style="width:700px;">';
			if($consentPurgeStatus=="true"){
				$table.='<tr><td style="color:#FF0000;width:700px; height:20px;" class="cbold"><u>CONSENT FORM PURGED</u></td></tr>';
			}else{
				$table.='<tr><td class="bdrbtm">&nbsp;</td></tr>';
			}
	$table.='</table>';
			   
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$table);
fclose($fileOpen);

?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
	if(top.document.getElementById("hiddScanPdfId")) {
		top.document.getElementById("hiddScanPdfId").value = 'pdfDelete';
	}
	if(top.document.getElementById("intConsentTemplateId")) {
		top.document.getElementById("intConsentTemplateId").value = '<?php echo $consentMultipleId;?>';
	}	
</script>
<table style="font:vetrdana; font-size:14;" width="100%" height="100%" bgcolor="#FFFFFF">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="images/pdf_load_img.gif"></td> 
	</tr>
</table>	
<form name="printFrm" action="new_html2pdf/createPdf.php?op=p" method="post">
</form>		
<script type="text/javascript">
 submitfn();
</script>
</body>