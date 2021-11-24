<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	session_start();
	include_once("common/conDb.php");
	include_once("common_functions.php");
	include_once("common/commonFunctions.php"); 
	//include("common/linkfile.php");
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
	$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
?>
<style>
	body { text-align:center; margin: 0px; background: #ECF1EA; width:100%; font:normal 11px Verdana, Arial, sans-serif;color:#000}
</style>	
<body >
<?php	
	$get_http_path=$_REQUEST['get_http_path'];
	include_once("new_header_print.php");
	//$lable="Patient Consent";
	$pConfId = $_REQUEST['pConfId'];
		
	
	$consentMultipleId  =$_REQUEST['consentMultipleId'];
	$consentMultipleAutoIncrId = $_REQUEST['consentMultipleAutoIncrId'];
	
	if($consentMultipleAutoIncrId){
		$consentAutoIncrIdQry = " AND surgery_consent_id='".$consentMultipleAutoIncrId."'";
	}	
	
	$Consent_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$_REQUEST["pConfId"]."'";
	$Consent_patientConfirm_tblRes = imw_query($Consent_patientConfirm_tblQry) or die(imw_error());
	$Consent_patientConfirm_tblRow = imw_fetch_array($Consent_patientConfirm_tblRes);
	$patient_id = $Consent_patientConfirm_tblRow["patientId"];
	//GET PATIENT DETAIL
	$Consent_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
	$Consent_patientName_tblRes = imw_query($Consent_patientName_tblQry) or die(imw_error());
	$Consent_patientName_tblRow = imw_fetch_array($Consent_patientName_tblRes);
	$Consent_patientName = $Consent_patientName_tblRow["patient_lname"].", ".$Consent_patientName_tblRow["patient_fname"]." ".$Consent_patientName_tblRow["patient_mname"];
	
	//START GET ARRIVAL TIME
	$stubWaitingDetailArr = $objManageData->getStubWaitingDetail($_REQUEST['pConfId'],$Consent_patientConfirm_tblRow["dos"])	;
	$arrivalTime = ($stubWaitingDetailArr[0]) ? $stubWaitingDetailArr[0] : '';	
	//END GET ARRIVAL TIME
	
	$Consent_patientNameDobTemp = $Consent_patientName_tblRow["date_of_birth"];
		$Consent_patientNameDob_split = explode("-",$Consent_patientNameDobTemp);
		$Consent_patientNameDob = $Consent_patientNameDob_split[1]."-".$Consent_patientNameDob_split[2]."-".$Consent_patientNameDob_split[0];
	

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
	
	//VIEW RECORD FROM DATABASE 
	$ViewConsentSurgeryQry = "select * from `consent_multiple_form` where  confirmation_id = '".$_REQUEST["pConfId"]."' AND consent_template_id='".$consentMultipleId."' ".$consentAutoIncrIdQry." AND consent_template_id!='0'";
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
$preid=$pConfId;
$pertable = 'surgery_consent_form';
$preidName = 'confirmation_id';
$predocSign = 'surgery_consent_sign';
$qry = "select surgery_consent_sign from consent_multiple_form where confirmation_id = $pConfId and consent_template_id = '".$consentMultipleId."'";
$pixleRes = imw_query($qry);
list($surgery_consent_sign) = imw_fetch_array($pixleRes);
require_once("imgGd.php");
drawOnImage($surgery_consent_sign,$imgName,'123.jpg');
/*******End of signature*****/
//protocol AND serverAddress VARIABLES USED FOR REPLACEMENT OF IMAGES INTO IOLINK AND IASCEMR
$protocol = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'; 
$serverAddress = $_SERVER['SERVER_NAME'];

	//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE
		$surgery_consent_data= str_ireplace("&#39;","'",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{PATIENT ID}","<b>".$Consent_patientName_tblRow["patient_id"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{PATIENT FIRST NAME}","<b>".$Consent_patientName_tblRow["patient_fname"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{MIDDLE INITIAL}","<b>".$Consent_patientName_tblRow["patient_mname"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{LAST NAME}","<b>".$Consent_patientName_tblRow["patient_lname"]."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{DOB}","<b>".$Consent_patientNameDob."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{SURGEON NAME}","<b>".$Consent_patientConfirmSurgeon."</b>",$surgery_consent_data);
		$surgery_consent_data= str_ireplace("{ARRIVAL TIME}", '<b>'.$arrivalTime.'</b>', $surgery_consent_data);
		$surgery_consent_data= str_replace("{SITE}","<b>".$Consent_patientConfirmSite."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{PROCEDURE}","<b>".$Consent_patientConfirmPrimProc."</b>",$surgery_consent_data);
		$surgery_consent_data= str_replace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$surgery_consent_data);
		$surgery_consent_data = str_replace('{SIGNATURE}',"",$surgery_consent_data);
		
		$surgery_consent_data = str_replace('{TEXTBOX_XSMALL}',"<input type='text' name='xsmall' size='1' maxlength='1'>",$surgery_consent_data);
		$surgery_consent_data = str_replace('{TEXTBOX_SMALL}',"<input type='text' name='small' size='30' maxlength='30'>",$surgery_consent_data);
		$surgery_consent_data = str_replace('{TEXTBOX_MEDIUM}',"<input type='text' name='medium' size='60' maxlength='60'>",$surgery_consent_data);
		$surgery_consent_data = str_replace('{TEXTBOX_LARGE}',"<textarea name='large' cols='100' rows='2'></textarea>",$surgery_consent_data);
		$surgery_consent_data = str_replace('SigPlus_images/',"../SigPlus_images/",$surgery_consent_data);
		$surgery_consent_data = str_replace('html2pdfnew/sign_',"../html2pdfnew/sign_",$surgery_consent_data);
		$surgery_consent_data = str_ireplace($protocol.$serverAddress.'/'.$surgeryCenterDirectoryName.'/new_html2pdf/',"",$surgery_consent_data);
		$surgery_consent_data = str_ireplace('/'.$surgeryCenterDirectoryName.'/new_html2pdf/',"",$surgery_consent_data);
		/*
		if(stristr('html2pdfnew/sign_',$surgery_consent_data)==true) {
			$surgery_consent_data_explode = explode('html2pdfnew/sign_',$surgery_consent_data);
			if(!$fileexist($sdfsd)) {
				copy('',$sdfsd);
			}
		}
		*/
		//SET TEXT BOX VALUES
		$surgery_consent_data = str_replace('<input type="text" name="xsmall" value="','',$surgery_consent_data);
		$surgery_consent_data = str_replace('" size="1" >','',$surgery_consent_data);
	
		$surgery_consent_data = str_replace('<input type="text" name="small" value="','',$surgery_consent_data);
		$surgery_consent_data = str_replace('" size="30" >','',$surgery_consent_data);

		$surgery_consent_data = str_replace('<input type="text" name="medium" value="','',$surgery_consent_data);
		$surgery_consent_data = str_replace('" size="60" >','',$surgery_consent_data);
		
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
		//REPLACE MULTIPLE MEDIUM TEXTBOX

		//END SET TEXT BOX VALUES
	//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
//END VIEW RECORD FROM DATABASE
	
	
		
	
	$table.=$head_table."\n";
	
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

	
	if($signSurgeon1Activate=='yes' || $signNurseActivate=='yes') {
		$surgery_consent_data.='
			<table style="width:700px;" cellpadding="0" cellspacing="0">
				<tr>
					<td style="width:400px;">';
				if($signSurgeon1Activate=='yes' && $signSurgeon1ConsentId){
					$surgery_consent_data.='
									<b>Surgeon:</b> Dr '.$Surgeon1ConsentName.'
									<br><b>Electronically Signed :&nbsp;</b>'.$Surgeon1ConsentSignOnFileStatus.'
									<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signSurgeon1DateTime);
				}
				$surgery_consent_data.='
					</td>
					<td style="width:300px;">';
				if($signNurseActivate=='yes' && $signNurseConsentId){ 
					$surgery_consent_data.='
									<b>Nurse:</b> '.$NurseConsentNameShow.'
									<br><b>Electronically Signed :&nbsp;</b>'.$NurseConsentSignOnFileStatus.'
									<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signNurseDateTime);
				}
		$surgery_consent_data.='
					</td>
				</tr>
			</table>';
	}
		 
	//END CODE FOR SURGEON AND NURSE SIGN

	$signAnesthesia1Activate 	= $ViewConsentSurgeryRow["signAnesthesia1Activate"];
	$signAnesthesia1Id 			= $ViewConsentSurgeryRow["signAnesthesia1Id"];
	$AnesthesiaConsentNameShow  = $ViewConsentSurgeryRow["signAnesthesia1LastName"].", ".$ViewConsentSurgeryRow["signAnesthesia1FirstName"]." ".$ViewConsentSurgeryRow["signAnesthesia1MiddleName"];
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
			<table cellpadding="0" cellspacing="0" style="width:700px; margin-top:5px;">
				<tr>
					<td nowrap align="left" style="width:400px;" >
			';
				if($signAnesthesia1Activate=='yes' && $signAnesthesia1Id) { 
					$Anesthesia1PreFix = 'Dr';
					$Anesthesia1SubType = getUserSubTypeFun($signAnesthesia1Id); //FROM common/commonFunctions.php
					if($Anesthesia1SubType=='CRNA') {$Anesthesia1PreFix = ''; }
					$surgery_consent_data.='
									<b>Anesthesia Provider:</b> '.$Anesthesia1PreFix.' '.$AnesthesiaConsentNameShow.'
									<br><b>Electronically Signed :&nbsp;</b>'.$signAnesthesia1Status.'
									<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signAnesthesia1DateTime);
				}
				$surgery_consent_data.='
					</td>
					<td nowrap align="left" style="width:300px;">';
				if($signWitness1Activate=='yes' && $signWitness1Id) { 
					$surgery_consent_data.='
						<b>Witness:</b> '.$WitnessConsentNameShow.'
						<br><b>Electronically Signed :&nbsp;</b>'.$signWitness1Status.'
						<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($signWitness1DateTime);
				}
				$surgery_consent_data.='
					</td>			
				</tr>
			</table>';
	}
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
		$fileConsent 			= $rootServerPath."/".$surgeryCenterDirectoryName."/html2pdfnew/consent_image_".$pConfId.".".$image_type;
		file_put_contents($fileConsent, $image_base64);
		$fileConsent = str_ireplace(".".$image_type,".jpg",$fileConsent);
		$bakImgResource = imagecreatefromstring($image_base64);
		imagejpeg($bakImgResource,$fileConsent);
		$fileConsentImgSrc = str_ireplace($rootServerPath."/".$surgeryCenterDirectoryName."/html2pdfnew/","../html2pdfnew/",$fileConsent);
		$surgery_consent_data = str_ireplace(trim("data:image/".$image_type.";base64,".$image_parts_new),trim($fileConsentImgSrc),$surgery_consent_data);
	}
	//END CODE TO DECODE THE SRC OF IMAGE


	$table.= strip_tags($surgery_consent_data,'<img> <br> <strong> <u> <b> <p> <div> <table> <tbody> <tr> <td> <span> ');
	$table = str_ireplace('&nbsp;<br />','',$table);
	$table = str_ireplace('<p>&nbsp;</p>','',$table);
	$table = str_ireplace('</b> <b>','</b>&nbsp;<b>',$table);
	$table = str_ireplace('/'.$surgeryCenterDirectoryName.'/new_html2pdf/','',$table);



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
	function submitfn(){
		document.printFrm.submit();
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