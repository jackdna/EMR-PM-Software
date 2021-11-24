<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes= imw_query($SurgeryQry);
$SurgeryRecord=imw_fetch_array($SurgeryRes);
$name= $SurgeryRecord['name'];
$address= $SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state'];
$img = $SurgeryRecord['logoName'];

function dateDiffHeader($dformat, $endDate, $beginDate){
	$date_parts1=explode($dformat, $beginDate);
	$date_parts2=explode($dformat, $endDate);
	$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
	$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
	return $end_date - $start_date;
}
$Confirm_patientHeaderInfo = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $_REQUEST["pConfId"]);
$Confirm_patientHeaderPatientId = $Confirm_patientHeaderInfo->patientId;


$sessPatientId = $Confirm_patientHeaderPatientId;
if(!$sessPatientId) {
	$sessPatientId = $_SESSION['patient_id']; 
}

$patientHeaderInfo = $objManageData->getRowRecord('patient_data_tbl', 'patient_id', $sessPatientId);
$patientHeaderFname = $patientHeaderInfo->patient_fname;
$patientHeaderMname = $patientHeaderInfo->patient_mname;
$patientHeaderNname = $patientHeaderInfo->patient_lname;
$patientHeaderName = $patientHeaderNname.", ".$patientHeaderFname;
	
$patientHeaderstreet1 = stripslashes($patientHeaderInfo->street1);	
$patientHeaderstreet2 = stripslashes($patientHeaderInfo->street2);	
$patientHeadercity = $patientHeaderInfo->city;	
//if(strlen($patientHeadercity)>7){ $patientHeadercity = substr($patientHeadercity,0,7);}
$patientHeaderstate = $patientHeaderInfo->state;	
$patientHeaderzip = $patientHeaderInfo->zip;	
$patientHeaderdate_of_birth = $patientHeaderInfo->date_of_birth;	
		$patientHeader_dob_split = explode("-",$patientHeaderdate_of_birth);
		$patientHeader_dob = $patientHeader_dob_split[1]."-".$patientHeader_dob_split[2]."-".$patientHeader_dob_split[0];

	//CODE TO CALCULATE AGE OF PATIENT
	if($patientHeaderInfo->date_of_birth!="" && $patientHeaderInfo->date_of_birth!="0000-00-00"){
		$tmpHeader_date = $patientHeaderInfo->date_of_birth;
		
		$patientHeader_age=$objManageData->dob_calc($tmpHeader_date);
		
	}
	//END CODE TO CALCULATE AGE OF PATIENT


$patientHeadersexTemp = $patientHeaderInfo->sex;
	$patientHeadersex='';
	if($patientHeadersexTemp=="m") { $patientHeadersex = "Male";} else if($patientHeadersexTemp=="f") { $patientHeadersex = "Female";  }	
$patientHeaderhomePhone = $patientHeaderInfo->homePhone;	
$patientHeaderworkPhone = $patientHeaderInfo->workPhone;
	if($patientHeaderhomePhone<>"") { $patientHeaderPhone =  $patientHeaderhomePhone;}else { $patientHeaderPhone = $patientHeaderworkPhone; }


$patientHeaderID = $patientHeaderInfo->patient_id;

//$Confirm_patientHeaderInfo = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $_REQUEST["pConfId"]);
$Confirm_patientHeaderAscID = $Confirm_patientHeaderInfo->ascId;
$Confirm_patientHeaderAdvanceDirective = $Confirm_patientHeaderInfo->advanceDirective;
$Confirm_patientHeaderDosTemp = $Confirm_patientHeaderInfo->dos;
		$Confirm_patientHeaderDos_split = explode("-",$Confirm_patientHeaderDosTemp);
		$Confirm_patientHeaderDos = $Confirm_patientHeaderDos_split[1]."-".$Confirm_patientHeaderDos_split[2]."-".$Confirm_patientHeaderDos_split[0];
$Confirm_patientHeaderSurgeon_name = $Confirm_patientHeaderInfo->surgeon_name;
$Confirm_patientHeaderAnesthesiologist_name = $Confirm_patientHeaderInfo->anesthesiologist_name;

$Confirm_patientHeaderSiteTemp = $Confirm_patientHeaderInfo->site;
	//APPLYING NUMBERS TO PATIENT SITE
		if($Confirm_patientHeaderSiteTemp == 1) {
			$Confirm_patientHeaderSite = "Left Eye";  //OD
		}else if($Confirm_patientHeaderSiteTemp == 2) {
			$Confirm_patientHeaderSite = "Right Eye";  //OS
		}else if($Confirm_patientHeaderSiteTemp == 3) {
			$Confirm_patientHeaderSite = "Both Eye";  //OU
		}
	//END APPLYING NUMBERS TO PATIENT SITE

$Confirm_patientHeaderPrimProc = $Confirm_patientHeaderInfo->patient_primary_procedure;
//if(strlen($Confirm_patientHeaderPrimProc)>20){ $Confirm_patientHeaderPrimProc = substr($Confirm_patientHeaderPrimProc,0,20)."... ";}
$Confirm_patientHeaderSecProc = $Confirm_patientHeaderInfo->patient_secondary_procedure;
//if(strlen($Confirm_patientHeaderSecProc)>10){ $Confirm_patientHeaderSecProc = substr($Confirm_patientHeaderSecProc,0,10)."... ";}
$Confirm_patientHeaderAssist_by_translator = $Confirm_patientHeaderInfo->assist_by_translator;

//check whether procedure is laser procedure or not
	$str_procedure_category = "SELECT * FROM procedures WHERE name = '".$Confirm_patientHeaderPrimProc."'";
	$qry_procedure_category = imw_query($str_procedure_category);
	$fetchRows_procedure_category = imw_fetch_array($qry_procedure_category);
	$patient_print_consent_categoryID = $fetchRows_procedure_category['catId'];
	if($patient_print_consent_categoryID!=2)
	{
		$Confirm_patientHeaderAnesthesiologist_name_print=$Confirm_patientHeaderAnesthesiologist_name;
	}
	else
	{
		$Confirm_patientHeaderAnesthesiologist_name_print="N/A";
	}
//end check whether procedure is laser procedure or not


//SET ALLERGIES VALUE
	$patient_allergies_tblQry = "SELECT `allergy_name` FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '".$_REQUEST["pConfId"]."'";
	$patient_allergies_tblRes = imw_query($patient_allergies_tblQry) or die(imw_error());
	$patient_allergies_tblNumRow = imw_num_rows($patient_allergies_tblRes);
	if($patient_allergies_tblNumRow>0) {
		//$allergiesValue = '<img src="images/Interface_red_image003.gif" width="17" height="15" align="middle" onclick="showAllergiesPopUpFn();">';
		$patient_allergies_tblRow = imw_fetch_array($patient_allergies_tblRes);
		$patientHeaderAllergyName = $patient_allergies_tblRow['allergy_name'];
		if(trim($patientHeaderAllergyName)=='NKA' && $patient_allergies_tblNumRow==1) {
			$allergiesValue = 'NKA';
		}else {
		
			$allergiesValue = 'Has Allergies';
		}
	}else {
		$allergiesValue = 'NKA';
	}
//END SET ALLERGIES VALUE

	if($patient_print_consent_categoryID!=2)
	{
		$selectPreOpNursingHeaderQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectPreOpNursingHeaderRes = imw_query($selectPreOpNursingHeaderQry) or die(imw_error());
		$selectPreOpNursingHeaderNumRow = imw_num_rows($selectPreOpNursingHeaderRes);
	    
		$vitalSignBp_NursingHeader = "";
		$vitalSignP_NursingHeader = "";
		$vitalSignR_NursingHeader = "";
		$vitalSignO2SAT_NursingHeader = "";
		$vitalSignTemp_NursingHeader = "";
		if($selectPreOpNursingHeaderNumRow>0) {
			$selectPreOpNursingHeaderRow = imw_fetch_array($selectPreOpNursingHeaderRes);
			$preopnursing_vitalsign_id = $selectPreOpNursingHeaderRow["preopnursing_vitalsign_id"];
			$vitalSignHeight_NursingHeader		=	$selectPreOpNursingHeaderRow["patientHeight"]	;
			$vitalSignHeight_NursingHeader		.=	!empty($vitalSignHeight_NursingHeader)	?	'"' : '';
			$vitalSignWeight_NursingHeader	=	$selectPreOpNursingHeaderRow["patientWeight"]	;
			$vitalSignWeight_NursingHeader	.=	!empty($vitalSignWeight_NursingHeader)	?	' lbs' : '';
			
			if($preopnursing_vitalsign_id) { 
				$ViewPreopNurseVitalHeaderSignQry = "select * from `preopnursing_vitalsign_tbl` where  vitalsign_id = '".$preopnursing_vitalsign_id."'";
				$ViewPreopNurseVitalHeaderSignRes = imw_query($ViewPreopNurseVitalHeaderSignQry) or die(imw_error()); 
				$ViewPreopNurseVitalHeaderSignNumRow = imw_num_rows($ViewPreopNurseVitalHeaderSignRes);
				
				if($ViewPreopNurseVitalHeaderSignNumRow>0) {
					$ViewPreopNurseVitalHeaderSignRow = imw_fetch_array($ViewPreopNurseVitalHeaderSignRes);
				
					$vitalSignBp_NursingHeader = $ViewPreopNurseVitalHeaderSignRow["vitalSignBp"];
					$vitalSignP_NursingHeader = $ViewPreopNurseVitalHeaderSignRow["vitalSignP"];
					$vitalSignR_NursingHeader = $ViewPreopNurseVitalHeaderSignRow["vitalSignR"];
					$vitalSignO2SAT_NursingHeader = $ViewPreopNurseVitalHeaderSignRow["vitalSignO2SAT"];
					$vitalSignTemp_NursingHeader = $ViewPreopNurseVitalHeaderSignRow["vitalSignTemp"];
				}
			}
			
			
			/*$vitalSignBp_NursingHeader = $selectPrmeOpNursingHeaderRow["vitalSignBp"];
			$vitalSignP_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignP"];
			$vitalSignR_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignR"];
			$vitalSignO2SAT_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignO2SAT"];
			$vitalSignTemp_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignTemp"];
			*/
			if($vitalSignTemp_NursingHeader<>"") {
				$vitalSignTemp_NursingHeader = $vitalSignTemp_NursingHeader;
			}
			
		}
	}else{
		$selectLaserProedureHeaderQry = "SELECT * FROM `laser_procedure_patient_table` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
		$selectLaserProedureHeaderRes = imw_query($selectLaserProedureHeaderQry) or die(imw_error());
		$selectLaserProedureHeaderNumRow = imw_num_rows($selectLaserProedureHeaderRes);
	
		if($selectLaserProedureHeaderNumRow>0) {
			$selectlaserProcedureRow = imw_fetch_array($selectLaserProedureHeaderRes);
			$vitalSignBp_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignBP"];
			$vitalSignP_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignP"];
			$vitalSignR_NursingHeader = $selectlaserProcedureRow["prelaserVitalSignR"];
			$vitalSignO2SAT_NursingHeader = 'N/A';
			$vitalSignTemp_NursingHeader = 'N/A';
			$vitalSignHeight_NursingHeader		=	'N/A';
			$vitalSignWeight_NursingHeader	=	'N/A';
		}
	
	}	
//set surgerycenter detail
			
			$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
			$SurgeryRes= imw_query($SurgeryQry);
			while($SurgeryRecord=imw_fetch_array($SurgeryRes))
			{
		    $nameSur= $SurgeryRecord['name'];
			$address= stripslashes($SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state']);
			$img = $SurgeryRecord['logoName'];
			$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
			}
//$file=fopen('html2pdf/white.jpg','w+');
//fputs($file,$surgeryCenterLogo);
	$size=getimagesize('html2pdf/white.jpg');
$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
imagejpeg($bakImgResource,'html2pdf/white.jpg');
imagejpeg($bakImgResource,'white.jpg');
$size=getimagesize('html2pdf/white.jpg');	
	 $hig=$size[1];
	 $wid=$size[0];
//Set size for logo image
$filename='html2pdf/white.jpg';
 function showThumbImages($fileName='white.jpg',$targetWidth=500,$targetHeight=70)
{ 

if(file_exists($fileName))
{ 
$img_size=getimagesize('html2pdf/white.jpg');
 $width=$img_size[0];
 $height=$img_size[1];
 $filename;
do
{
if($width > $targetWidth)
{
 $width=$targetWidth;
 $percent=$img_size[0]/$width;
 $height=$img_size[1]/$percent; 
}
if($height > $targetHeight)
{
$height=$targetHeight;
$percent=$img_size[1]/$height;
 $width=$img_size[0]/$percent; 
}

}while($width > $targetWidth || $height > $targetHeight);

$returnArr[] = "<img src='white.jpg' width='$width' height='$height'>";
$returnArr[] = $width;
$returnArr[] = $height;
return $returnArr; 

} 
return "";
}				

//	 
$img_logo= showThumbImages('html2pdf/white.jpg',170,50);

$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+8;
//print_r($img_logo);
 $Ptcompleteaddress=stripslashes($patientHeadercity.",".$patientHeaderstate." ".$patientHeaderzip);
 $Ptcompleteaddressnum = strlen($Ptcompleteaddress);
 $v=$patientHeaderstreet1;
 $nn = strlen($v);
if($Ptcompleteaddressnum<=19)
{
  $td='20';
} 
else
{
 $td='40';
}
if($nn<=29)
{
 $tdheight= '35';
 
}
else
{
 $tdheight= '50';

}
if($fromFile == "operative_recordPdf"){	
	$head_table.='<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
				<tr height="30" >
					<td style="font-size:9px; " valign="top"  align="left"   valign="bottom" ><b>'.$name.'<br>'.$address.'</b></td>
					<td style="font-size:9px; "  id="img_h" valign="top"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
				</tr>
				<tr><td style="font-size:9px; " valign="top" colspan="2" height="3">&nbsp;</td></tr>
				<tr><td style="font-size:9px; " valign="top" colspan="2" bgcolor="#000000" height="3">&nbsp;</td></tr>
				<tr><td style="font-size:9px; " valign="top" colspan="2" height="3">&nbsp;</td></tr>
				
			</table>'."\n";
}
else{
	$head_table.='<table align="center" style="width: 100%; "  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
				<tr height="30" >
					<td style="font-size:9px; " valign="top"  align="left" valign="bottom" ><b>'.$name.'<br>'.$address.'</b></td>
					<td style="font-size:9px; " id="img_h" valign="top"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
				</tr>
				<tr><td style="font-size:9px;width: 100%; " valign="top" colspan="2" height="1"></td></tr>
				<tr><td style="font-size:9px;width: 100%; " valign="top" colspan="2" bgcolor="#000000" height="1"></td></tr>
				<tr><td style="font-size:9px;width: 100%; " valign="top" colspan="2" height="1"></td></tr>
				
			</table>'."\n";	
	$head_table.='<table cellpadding="0" style="width: 100%; " cellspacing="0" border="0"   >
					<tr>
						<td style="font-size:9px; " valign="top" height="15" align="left" width="12%"><strong>Patient&nbsp;Name:</strong></font></td>
						<td style="font-size:9px; " valign="top" height="15" align="left" width="17%">'.$patientHeaderName.'</font></td>
						<td style="font-size:9px; " valign="top" height="15" width="19%"><strong>ASC</strong>&nbsp;'.$Confirm_patientHeaderAscID.'</font></td>
						<td style="font-size:9px; " valign="top" height="15" width="25%"><strong>Surgeon</strong>&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderSurgeon_name.'</font></td>
						<td style="font-size:9px; " valign="top" height="15" width="27%"><strong>Surgery Date&nbsp; </strong>'.$Confirm_patientHeaderDos.'</font></td>
					</tr>
					<tr>
						<td style="font-size:9px; " valign="top" height="'.$tdheight.'" ><strong>Address:</strong>&nbsp;&nbsp;&nbsp;</font></td>
						<td style="font-size:9px; " valign="top" rowspan="2" ><table style="width: 100%; " >
											<tr>
												<td style="font-size:9px; " valign="top">'.$patientHeaderstreet1.'</td>
											</tr>
											<tr>
												<td style="font-size:9px; " valign="top">'.$patientHeadercity.'</td>
											</tr>
											<tr>
												<td style="font-size:9px; " valign="top">'.$patientHeaderstate.$patientHeaderzip.'</td>
											</tr>
										</table></td>
						
						<td style="font-size:9px; " valign="top"  ><strong>DOB</strong>&nbsp;'.$patientHeader_dob.'</font></td>
						<td style="font-size:9px; " valign="top"  ><strong>Age</strong>&nbsp;&nbsp;&nbsp;'.$patientHeader_age.'yrs</font></td>
						<td style="font-size:9px; " valign="top" ><strong>Sex</strong>&nbsp;&nbsp;&nbsp;'.$patientHeadersex.'</font></td>
					</tr>
					
					<tr>
						<td style="font-size:9px; " valign="top">&nbsp;</td>						
						<td style="font-size:9px; " valign="top"><strong>Tel.</strong>&nbsp;'.$patientHeaderPhone.'</font></td>
						<td style="font-size:9px; " valign="top"><strong>Allergies&nbsp;</strong>&nbsp;&nbsp;&nbsp;'.$allergiesValue.'</font></td>
						<td style="font-size:9px; " valign="top"><strong>Site</strong>&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderSite.'</font></td>
					</tr>
					<tr>
						<td style="font-size:9px; " valign="top"  colspan="3" height="15"><strong>Anesthesia Provider</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderAnesthesiologist_name_print.'</font></td>
						<td style="font-size:9px; " valign="top" height="15">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>A/D</strong>&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderAdvanceDirective.'</font></td>
						<td style="font-size:9px; " valign="top" height="15"><strong>Assist. by Translator&nbsp;&nbsp;&nbsp;&nbsp;</strong>&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderAssist_by_translator.'</font></td>
					</tr>
					<tr>
						<td style="font-size:9px; " valign="top"  colspan="3"><strong>Primary Proc.&nbsp;&nbsp;&nbsp;</strong> &nbsp;&nbsp;'.$Confirm_patientHeaderPrimProc.'</font></td>
						<td style="font-size:9px; " valign="top"  colspan="2"><strong>Secondary Proc.&nbsp;&nbsp;&nbsp;</strong> &nbsp;&nbsp;'.$Confirm_patientHeaderSecProc.'</font></td>
					</tr>
					<tr >
						<td style="font-size:9px; " valign="top" height="8" colspan="5"></td>
					</tr>
				</table>';
	$head_table.='<table cellpadding="0" style="width: 100%; " cellspacing="0" align="center"  border="1">
					
					<tr>
						<td style="font-size:9px;width: 14.5%; " valign="top"  height="15" ><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="font-size:9px; " valign="top" width="130">Base Line Vital Signs</font></td></tr></table></td>
						<td style="font-size:9px;width: 12.5%; " valign="top"  height="15" ><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="font-size:9px; " valign="top" width="102">B/P&nbsp;&nbsp;&nbsp;&nbsp;'.$vitalSignBp_NursingHeader.'</font></td></tr></table></td>
						<td style="font-size:9px;width: 11.5%; " valign="top"  height="15" ><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="font-size:9px; " valign="top" width="102">P&nbsp;&nbsp;&nbsp;'.$vitalSignP_NursingHeader.'</font></td></tr></table></td>
						<td style="font-size:9px;width: 11.5%; " valign="top"  height="15" ><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="font-size:9px; " valign="top" width="102">R&nbsp;&nbsp;&nbsp;'.$vitalSignR_NursingHeader.'</font></td></tr></table></td>
						<td style="font-size:9px;width: 12.5%; " valign="top"  height="15" ><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="font-size:9px; " valign="top" width="102">O<sub>2</sub>SAT&nbsp;&nbsp;&nbsp;'.$vitalSignO2SAT_NursingHeader.'</font></td></tr></table></td>
						<td style="font-size:9px;width: 12.5%; " valign="top"  height="15" ><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="font-size:9px; " valign="top" width="102">Temp&nbsp;&nbsp;&nbsp;'.$vitalSignTemp_NursingHeader.'</font></td></tr></table></td>
						<td style="font-size:9px;width: 12.5%; " valign="top"  height="15" ><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="font-size:9px; " valign="top" width="102">Height&nbsp;&nbsp;&nbsp;'.$vitalSignHeight_NursingHeader.'</font></td></tr></table></td>
						<td style="font-size:9px;width: 12.5%; " valign="top"  height="15" ><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="font-size:9px; " valign="top" width="102">Weight&nbsp;&nbsp;&nbsp;'.$vitalSignWeight_NursingHeader.'</font></td></tr></table></td>
					</tr>
				 </table>';
}
?>