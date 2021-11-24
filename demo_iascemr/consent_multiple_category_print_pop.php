<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	session_start();
	include_once("common/conDb.php");
	include("common_functions.php");
	//include("common/linkfile.php");
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
	include_once("new_header_print.php");
	//$lable="Patient Consent";
	$pConfId = $_REQUEST['pConfId'];
	if(!$pConfId) {
		$pConfId = $_SESSION['pConfId']; 
	}
	$consent_categoryId=$_REQUEST['categoryId'];
?>
<style>
	body { text-align:center; margin: 0px; background: #ECF1EA; width:100%; font:normal 11px Verdana, Arial, sans-serif;color:#000}
</style>
<?php 	
$header='<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
				<tr height="30" >
					<td  align="left"   valign="bottom" ><b>'.$name.'<br>'.$address.'</b></td>
					<td  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
				</tr>
				<tr><td colspan="2" height="3">&nbsp;</td></tr>
				<tr><td colspan="2" bgcolor="#000000" height="3">&nbsp;</td></tr>
				<tr><td colspan="2" height="3">&nbsp;</td></tr>
				
			</table>'."\n";
//	
	//GET PATIENT DETAIL
	$Consent_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$pConfId."'";
	$Consent_patientName_tblRes = imw_query($Consent_patientName_tblQry) or die(imw_error());
	$Consent_patientName_tblRow = imw_fetch_array($Consent_patientName_tblRes);
	$Consent_patientName = $Consent_patientName_tblRow["patient_lname"].", ".$Consent_patientName_tblRow["patient_fname"]." ".$Consent_patientName_tblRow["patient_mname"];
	
	
	$Consent_patientNameDobTemp = $Consent_patientName_tblRow["date_of_birth"];
		$Consent_patientNameDob_split = explode("-",$Consent_patientNameDobTemp);
		$Consent_patientNameDob = $Consent_patientNameDob_split[1]."-".$Consent_patientNameDob_split[2]."-".$Consent_patientNameDob_split[0];
	

	$Consent_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$pConfId."'";
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
	
	//VIEW RECORD FROM DATABASE 
	$ViewConsentSurgeryQry = "select * from `surgery_consent_form` where  confirmation_id = '".$pConfId."'";
	$ViewConsentSurgeryRes = imw_query($ViewConsentSurgeryQry) or die(imw_error()); 
	$ViewConsentSurgeryNumRow = @imw_num_rows($ViewConsentSurgeryRes);
	$ViewConsentSurgeryRow = imw_fetch_array($ViewConsentSurgeryRes); 
	
	$consentSurgery_patient_sign = $ViewConsentSurgeryRow["surgery_consent_sign"];
	$surgery_consent_data = stripslashes($ViewConsentSurgeryRow["surgery_consent_data"]);
	$form_status = $ViewConsentSurgeryRow["form_status"];
	$saveLink = $saveLink."&form_status=".$form_status;
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE	
	/*
	if(trim($surgery_consent_data)=="") {
		$ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = 1";
		$ViewConsentTemplateRes = imw_query($ViewConsentTemplateQry) or die(imw_error()); 
		$ViewConsentTemplateNumRow = imw_num_rows($ViewConsentTemplateRes);
		$ViewConsentTemplateRow = imw_fetch_array($ViewConsentTemplateRes); 
			
		$surgery_consent_data = stripslashes($ViewConsentTemplateRow["consent_data"]);
	}
	*/
	//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE		


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
		   
//*************************Start Multiple Consent Form**********************/
//START CODE FOR MULTIPLE CONSENT FORMS
	//VIEW RECORD FROM DATABASE
	$table='';
	$ViewConsentSurgeryQry = "select * from `consent_multiple_form` where  consent_category_id = '".$consent_categoryId."' and confirmation_id = '".$pConfId."' AND consent_template_id!='0' ORDER BY consent_template_id";
	$ViewConsentSurgeryRes = imw_query($ViewConsentSurgeryQry) or die(imw_error()); 
	$ViewConsentSurgeryNumRow = @imw_num_rows($ViewConsentSurgeryRes);
	if($ViewConsentSurgeryNumRow>0) {
		$cntrInc = 0;
		while($ViewConsentSurgeryRow = imw_fetch_array($ViewConsentSurgeryRes)) {
			$cntrInc++;
			
			$surgery_consent_data = stripslashes($ViewConsentSurgeryRow["surgery_consent_data"]);
			//FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE 
			$consentMultipleId = $ViewConsentSurgeryRow["consent_template_id"];

			$lable = $ViewConsentSurgeryRow["surgery_consent_alias"];
			$consentPurgeStatus = $ViewConsentSurgeryRow["consent_purge_status"];
			if(trim($surgery_consent_data)=="") {
				//DO NOT GET DATA FROM ADMIN PANEL
				/*
				$ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = '".$consentMultipleId."' AND consent_delete_status!='true'";
				$ViewConsentTemplateRes = imw_query($ViewConsentTemplateQry) or die(imw_error()); 
				$ViewConsentTemplateNumRow = imw_num_rows($ViewConsentTemplateRes);
				if($ViewConsentTemplateNumRow>0) {
					$ViewConsentTemplateRow = imw_fetch_array($ViewConsentTemplateRes); 
					
					$surgery_consent_data = stripslashes($ViewConsentTemplateRow["consent_data"]);
				}
				*/
			}
			
			//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
				$surgery_consent_data= str_replace("{PATIENT FIRST NAME}","<b>".$Consent_patientName_tblRow["patient_fname"]."</b>",$surgery_consent_data);
				$surgery_consent_data= str_replace("{MIDDLE INITIAL}","<b>".$Consent_patientName_tblRow["patient_mname"]."</b>",$surgery_consent_data);
				$surgery_consent_data= str_replace("{LAST NAME}","<b>".$Consent_patientName_tblRow["patient_lname"]."</b>",$surgery_consent_data);
				$surgery_consent_data= str_replace("{DOB}","<b>".$Consent_patientNameDob."</b>",$surgery_consent_data);
				$surgery_consent_data= str_replace("{DOS}","<b>".$Consent_patientConfirmDos."</b>",$surgery_consent_data);
				$surgery_consent_data= str_replace("{SURGEON NAME}","<b>".$Consent_patientConfirmSurgeon."</b>",$surgery_consent_data);
				$surgery_consent_data= str_replace("{SITE}","<b>".$Consent_patientConfirmSite."</b>",$surgery_consent_data);
				$surgery_consent_data= str_replace("{PROCEDURE}","<b>".$Consent_patientConfirmPrimProc."</b>",$surgery_consent_data);
				$surgery_consent_data= str_replace("{SECONDARY PROCEDURE}","<b>".$Consent_patientConfirmSecProc."</b>",$surgery_consent_data);
				$surgery_consent_data= str_replace("{DATE}","<b>".date('m-d-Y')."</b>",$surgery_consent_data);
				$surgery_consent_data = str_replace('{SIGNATURE}',"",$surgery_consent_data);
				
				$surgery_consent_data = str_replace('{TEXTBOX_XSMALL}',"<input type='text' name='xsmall' size='1' maxlength='1'>",$surgery_consent_data);
				$surgery_consent_data = str_replace('{TEXTBOX_SMALL}',"<input type='text' name='small' size='30' maxlength='30'>",$surgery_consent_data);
				$surgery_consent_data = str_replace('{TEXTBOX_MEDIUM}',"<input type='text' name='medium' size='60' maxlength='60'>",$surgery_consent_data);
				$surgery_consent_data = str_replace('{TEXTBOX_LARGE}',"<textarea name='large' cols='100' rows='2'></textarea>",$surgery_consent_data);
				$surgery_consent_data = str_replace('SigPlus_images/',"../SigPlus_images/",$surgery_consent_data);
				//$surgery_consent_data = str_replace('{SIGNATURE}',"",$surgery_consent_data);
				
				//SET TEXT BOX VALUES
				$surgery_consent_data = str_replace('<input type="text" name="xsmall" value="','',$surgery_consent_data);
				$surgery_consent_data = str_replace('" size="1" >','',$surgery_consent_data);
			
				$surgery_consent_data = str_replace('<input type="text" name="small" value="','',$surgery_consent_data);
				$surgery_consent_data = str_replace('" size="30" >','',$surgery_consent_data);
		
				$surgery_consent_data = str_replace('<input type="text" name="medium" value="','',$surgery_consent_data);
				$surgery_consent_data = str_replace('" size="60" >','',$surgery_consent_data);
				
				//REPLACE MULTIPLE VERY SMALL TEXTBOX
				/*$verySmallTextBoxExplode = explode('name="small',$surgery_consent_data);
				for($i=1;$i<count($verySmallTextBoxExplode);$i++) {
					
					$verySmallTextbox = 'xsmall'.$i;
					$surgery_consent_data = str_replace('<input type="text"  name="'.$verySmallTextbox.'" value="','',$surgery_consent_data);
					$surgery_consent_data = str_replace('" size="1"  maxlength="1">','',$surgery_consent_data);
				}*/
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
				
			//DISPLAY ALL CONSENT FORMS	
				/*
				if($cntrInc!='1') {
					if($table!='' && trim($surgery_consent_data)!='') {
						$table.='<newpage>';
					}
				}
				*/
				
				if(trim($surgery_consent_data)!='') {
					$table.="<page>";
					$table.=$head_table;
					$table.='<table style="width:700px;" cellpadding="0" cellspacing="0" >';
					
					if($consentPurgeStatus=="true"){
						$table.='<tr><td style="color:#FF0000;width:700px; height:20px;" class="cbold"><u>CONSENT FORM PURGED</u></td></tr>';
					}

				   $table.='<tr>
								<td style="width:700px;" class="fheader">'.$lable.'</td>
						   </tr>
						 </table>';
					
					//START CODE FOR SURGEON AND NURSE SIGN
					
					$signSurgeon1Activate = $ViewConsentSurgeryRow["signSurgeon1Activate"];
					$signSurgeon1ConsentId = $ViewConsentSurgeryRow["signSurgeon1Id"];
					$Surgeon1ConsentName = $ViewConsentSurgeryRow["signSurgeon1LastName"].", ".$ViewConsentSurgeryRow["signSurgeon1FirstName"]." ".$ViewConsentSurgeryRow["signSurgeon1MiddleName"];
					$Surgeon1ConsentSignOnFileStatus = $ViewConsentSurgeryRow["signSurgeon1Status"];
					
					$signNurseActivate = $ViewConsentSurgeryRow["signNurseActivate"];
					$signNurseConsentId = $ViewConsentSurgeryRow["signNurseId"];
					$NurseConsentNameShow = $ViewConsentSurgeryRow["signNurseLastName"].", ".$ViewConsentSurgeryRow["signNurseFirstName"]." ".$ViewConsentSurgeryRow["signNurseMiddleName"];
					$NurseConsentSignOnFileStatus = $ViewConsentSurgeryRow["signNurseStatus"];
					
					if($signSurgeon1Activate=='yes' || $signNurseActivate=='yes') {
						$surgery_consent_data.='
							<table cellpadding="0" cellspacing="0" style="width:700px;">
								<tr>
									<td style="width:400px;">';
								if($signSurgeon1Activate=='yes' && $signSurgeon1ConsentId) { 
									$surgery_consent_data.='
													<b>Surgeon:</b> Dr '.$Surgeon1ConsentName.'
													<br><b>Electronically Signed :&nbsp;</b>'.$Surgeon1ConsentSignOnFileStatus;
								}
								$surgery_consent_data.='
									</td>
									<td style="width:300px;">
								';
								if($signNurseActivate=='yes' && $signNurseConsentId) { 
									$surgery_consent_data.='
													<b>Nurse:</b> '.$NurseConsentNameShow.'
													<br><b>Electronically Signed :&nbsp;</b>'.$NurseConsentSignOnFileStatus;
								}
						$surgery_consent_data.='
									</td>
								</tr>
							</table>';
					}
						 
					//END CODE FOR SURGEON AND NURSE SIGN
					$signAnesthesia1Activate 	= $ViewConsentSurgeryRow["signAnesthesia1Activate"];
					$signAnesthesia1Id 			= $ViewConsentSurgeryRow["signAnesthesia1Id"];
					$AnesthesiaConsentNameShow = $ViewConsentSurgeryRow["signAnesthesia1LastName"].", ".$ViewConsentSurgeryRow["signAnesthesia1FirstName"]." ".$ViewConsentSurgeryRow["signAnesthesia1MiddleName"];
					$signAnesthesia1Status 		= $ViewConsentSurgeryRow["signAnesthesia1Status"];
					
					$signWitness1Activate 		= $ViewConsentSurgeryRow["signWitness1Activate"];
					$signWitness1Id 			= $ViewConsentSurgeryRow["signWitness1Id"];
					$WitnessConsentNameShow = $ViewConsentSurgeryRow["signWitness1LastName"].", ".$ViewConsentSurgeryRow["signWitness1FirstName"]." ".$ViewConsentSurgeryRow["signWitness1MiddleName"];
					$signWitness1Status 		= $ViewConsentSurgeryRow["signWitness1Status"];
				
				//	CODE FOR ANESTHESIA AND WITNESS SIGN
						if($signAnesthesia1Activate=='yes' || $signWitness1Activate=='yes') {
						$surgery_consent_data.='
							<table cellpadding="0" cellspacing="0" style="width:700px;">
								<tr>
									<td style="width:400px;">
							';
								if($signAnesthesia1Activate=='yes' && $signAnesthesia1Id) { 
									$surgery_consent_data.='
													<b>Anesthesiologist:</b> Dr '.$AnesthesiaConsentNameShow.'
													<br><b>Electronically Signed :&nbsp;</b>'.$signAnesthesia1Status;
								}
								$surgery_consent_data.='
									</td>
									<td style="width:300px;">';
								if($signWitness1Activate=='yes' && $signWitness1Id) { 
									$surgery_consent_data.='
													<b>Witness:</b> '.$WitnessConsentNameShow.'
													<br><b>Electronically Signed :&nbsp;</b>'.$signWitness1Status;
								}
						$surgery_consent_data.='
									</td>
								</tr>
							</table>';
					}
	//END CODE FOR ANESTHESIA AND WITNESS SIGN
					$table.= strip_tags($surgery_consent_data,'<img> <br> <u> <b> <p> <div> <table> <tbody> <tr> <td> ');					
					$table.='<table cellpadding="0" cellspacing="0" style="width:700px;">';
					if($consentPurgeStatus=="true"){
						$table.='<tr><td style="color:#FF0000;width:700px; height:20px;" class="cbold"><u>CONSENT FORM PURGED</u></td></tr>';
					}
					$table.='</table>';
					$table.='</page>';		   
				}	
			//END DISPLAY ALL CONSENT FORMS	
		}
	}	
	
//END CODE FOR MULTIPLE CONSENT FORMS
//*************************End Multiple Consent Form**********************/	
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$table);
fclose($fileOpen);
//$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';
/*echo"<script>window.open('testPdf.html?pConfId=','','')</script>";*/
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


<form name="printFrm"  action="new_html2pdf/createPdf.php?op=p" method="post">
</form>		
<script type="text/javascript">
	submitfn();
</script>
