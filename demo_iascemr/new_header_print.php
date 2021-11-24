<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
//get detail for logged in facility
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripslashes($dataFac->fac_name);
$address=stripslashes($dataFac->fac_address1).' '.stripslashes($dataFac->fac_address2).' '.stripslashes($dataFac->fac_city).' '.stripslashes($dataFac->fac_state);

$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes= imw_query($SurgeryQry);
$SurgeryRecord=imw_fetch_array($SurgeryRes);
if(!$name) {
	$name= $SurgeryRecord['name'];
	$address= $SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state'];
}
$img = $SurgeryRecord['logoName'];
$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
$nameSur=$name;


function dateDiffHeader($dformat, $endDate, $beginDate){
	$date_parts1=explode($dformat, $beginDate);
	$date_parts2=explode($dformat, $endDate);
	$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
	$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
	return $end_date - $start_date;
}
$Confirm_patientHeaderInfo = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $_REQUEST["pConfId"]);
$Confirm_patientHeaderPatientId = $Confirm_patientHeaderInfo->patientId;
$Confirm_patientHeaderAllergiesNKDA_status = $Confirm_patientHeaderInfo->allergiesNKDA_status;

$sessPatientId = $Confirm_patientHeaderPatientId;
if(!$sessPatientId) {
	$sessPatientId = $_SESSION['patient_id']; 
}

$patientHeaderInfo = $objManageData->getRowRecord('patient_data_tbl', 'patient_id', $sessPatientId);
$patientHeaderFname = $patientHeaderInfo->patient_fname;
$patientHeaderMname = $patientHeaderInfo->patient_mname;
$patientHeaderNname = $patientHeaderInfo->patient_lname;
$patientHeaderID = $patientHeaderInfo->patient_id;
$patientHeaderImwPatientId = $patientHeaderInfo->imwPatientId;
$imwPatientHeaderID = "";
if(constant("SHOW_IMW_PATIENT_ID")=="YES" && trim($patientHeaderImwPatientId)) {
	$imwPatientHeaderID = " / ".$patientHeaderImwPatientId;
}
	
$patientHeaderName = $patientHeaderNname.", ".$patientHeaderFname.' - '.$patientHeaderID.$imwPatientHeaderID;
	
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

$Confirm_patientHeaderPrimProcIsMisc	= $Confirm_patientHeaderInfo->prim_proc_is_misc;

$Confirm_patientHeaderSiteTemp = $Confirm_patientHeaderInfo->site;
	//APPLYING NUMBERS TO PATIENT SITE
		if($Confirm_patientHeaderSiteTemp == 1) {
			$Confirm_patientHeaderSite = "Left Eye";  //OD
		}else if($Confirm_patientHeaderSiteTemp == 2) {
			$Confirm_patientHeaderSite = "Right Eye";  //OS
		}else if($Confirm_patientHeaderSiteTemp == 3) {
			$Confirm_patientHeaderSite = "Both Eye";  //OU
		}else if($Confirm_patientHeaderSiteTemp == 4) {
			$Confirm_patientHeaderSite = "Left Upper Lid";
		}else if($Confirm_patientHeaderSiteTemp == 5) {
			$Confirm_patientHeaderSite = "Left Lower Lid";
		}else if($Confirm_patientHeaderSiteTemp == 6) {
			$Confirm_patientHeaderSite = "Right Upper Lid";
		}else if($Confirm_patientHeaderSiteTemp == 7) {
			$Confirm_patientHeaderSite = "Right Lower Lid";
		}else if($Confirm_patientHeaderSiteTemp == 8) {
			$Confirm_patientHeaderSite = "Bilateral Upper Lid";
		}else if($Confirm_patientHeaderSiteTemp == 9) {
			$Confirm_patientHeaderSite = "Bilateral Lower Lid";
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
	$patient_print_primary_procedure_id = $fetchRows_procedure_category['procedureId'];
	
	// Check Whether Procedure is Injection Procedure
	if($patient_print_consent_categoryID <> '2' )
	{
		if($Confirm_patientHeaderPrimProcIsMisc == '')
		{
			//$chkprocedurecatDetails = $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $patient_print_consent_categoryID);
			$Confirm_patientHeaderPrimProcIsMisc	=	$objManageData->verifyProcIsInjMisc($patient_print_primary_procedure_id);
			//($chkprocedurecatDetails->isMisc) ?	'true'	:	'';
		}
		
	}else
	{
		$Confirm_patientHeaderPrimProcIsMisc	=	'';
	}
	// Check Whether Procedure is Injection Procedure
	
	if($patient_print_consent_categoryID!=2)
	{
		if($Confirm_patientHeaderPrimProcIsMisc)
		{
			$Confirm_patientHeaderAnesthesiologist_name_print="N/A";	
		}
		else
		{
			$Confirm_patientHeaderAnesthesiologist_name_print=$Confirm_patientHeaderAnesthesiologist_name;
		}
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
		if(trim(strtoupper($patientHeaderAllergyName))=='NKA' && $patient_allergies_tblNumRow==1) {
			$allergiesValue = 'NKA';
		}else {
			$allergiesValue = 'Has Allergies';
		}
	}else if($Confirm_patientHeaderAllergiesNKDA_status=="Yes") {
		$allergiesValue = 'NKA';
	}else {
		$allergiesValue = '___';
	}
//END SET ALLERGIES VALUE

	if($patient_print_consent_categoryID!=2)
	{
		if($Confirm_patientHeaderPrimProcIsMisc)
		{
			$vitalSignBp_NursingHeader = "";
			$vitalSignP_NursingHeader = "";
			$vitalSignR_NursingHeader = "";
			$vitalSignO2SAT_NursingHeader = "";
			$vitalSignTemp_NursingHeader = "";
			$vitalSignHeight_NursingHeader = "";
			$vitalSignWeight_NursingHeader = "";
			$vitalSignBmi_NursingHeader	=	"";
			$vitalSignBgl_NursingHeader	=	"";
			
			$selectVitalSignCopyHeaderQry = "SELECT * FROM `injection` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
			$selectVitalSignCopyHeaderRes = imw_query($selectVitalSignCopyHeaderQry) or die($selectVitalSignCopyHeaderQry.imw_error());
			$selectVitalSignCopyHeaderNumRow = imw_num_rows($selectVitalSignCopyHeaderRes);
			if($selectVitalSignCopyHeaderNumRow>0) {
				$selectVitalSignCopyHeaderRow 	= imw_fetch_array($selectVitalSignCopyHeaderRes);
				$vitalSignBp_NursingHeader 			= $selectVitalSignCopyHeaderRow["preVitalBp"];
				$vitalSignP_NursingHeader 			= $selectVitalSignCopyHeaderRow["preVitalPulse"];
				$vitalSignR_NursingHeader 			= $selectVitalSignCopyHeaderRow["preVitalResp"];
				$vitalSignO2SAT_NursingHeader 		= $selectVitalSignCopyHeaderRow["preVitalSpo"];
				$vitalSignTemp_NursingHeader 		= 'N/A';
				$vitalSignHeight_NursingHeader		= "N/A";
				$vitalSignWeight_NursingHeader		= "N/A";
				$vitalSignBmi_NursingHeader			= "N/A";
				$vitalSignBgl_NursingHeader			= "N/A";
				
			}
		}
		else
		{
			
			$selectPreOpNursingHeaderQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '".$_REQUEST["pConfId"]."'";
			$selectPreOpNursingHeaderRes = imw_query($selectPreOpNursingHeaderQry) or die(imw_error());
			$selectPreOpNursingHeaderNumRow = imw_num_rows($selectPreOpNursingHeaderRes);
				
			$vitalSignBp_NursingHeader = "";
			$vitalSignP_NursingHeader = "";
			$vitalSignR_NursingHeader = "";
			$vitalSignO2SAT_NursingHeader = "";
			$vitalSignTemp_NursingHeader = "";
			$vitalSignHeight_NursingHeader = "";
			$vitalSignWeight_NursingHeader = "";
			$vitalSignBmi_NursingHeader	=	"";
			$vitalSignBgl_NursingHeader	=	"";
			
			if($selectPreOpNursingHeaderNumRow>0) {
				$selectPreOpNursingHeaderRow = imw_fetch_array($selectPreOpNursingHeaderRes);
				$preopnursing_vitalsign_id = $selectPreOpNursingHeaderRow["preopnursing_vitalsign_id"];
				$vitalSignHeight_NursingHeader		=	$selectPreOpNursingHeaderRow["patientHeight"]	;
				$vitalSignHeight_NursingHeader		.=	!empty($vitalSignHeight_NursingHeader)	?	'"' : '';
				$vitalSignWeight_NursingHeader	=	$selectPreOpNursingHeaderRow["patientWeight"]	;
				$vitalSignWeight_NursingHeader	.=	!empty($vitalSignWeight_NursingHeader)	?	' lbs' : '';
				$vitalSignBmi_NursingHeader	=	$selectPreOpNursingHeaderRow["patientBmi"]	;
				$vitalSignBgl_NursingHeader	=	trim($selectPreOpNursingHeaderRow["NA"]) ? "N/A" : trim(stripslashes($selectPreOpNursingHeaderRow["bsValue"]));
				
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
			$vitalSignO2SAT_NursingHeader 	= 'N/A';
			$vitalSignTemp_NursingHeader 	= 'N/A';
			$vitalSignHeight_NursingHeader 	= "N/A";
			$vitalSignWeight_NursingHeader 	= "N/A";
			$vitalSignBmi_NursingHeader		= "N/A";
			$vitalSignBgl_NursingHeader		= "N/A";
			
		}
	
	}	

//$file=fopen('html2pdf/white.jpg','w+');
//fputs($file,$surgeryCenterLogo);
	//$size=getimagesize('html2pdf/white.jpg');
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

$returnArr[] = "<img src='../html2pdf/white.jpg' width='$width' height='$height'>";
$returnArr[] = $width;
$returnArr[] = $height;
return $returnArr; 

} 
return "";
}				

//	 
$img_logo= showThumbImages('html2pdf/white.jpg',170,50);

$imgheight= number_format($img_logo[2]+8);
$imgwidth= number_format($img_logo[1]+8);
$headerImgLogo = $img_logo[0];

if($iDocOpNoteSave == "yes") {
	$imwAscemrImageDir = "ascemr_images";
	$imwAscemrDrawingFolder = $objManageData->getImedicDirPath($patientHeaderImwPatientId,$imwAscemrImageDir);
	$imwAscemrImageName = "ascemr_image_".$_REQUEST['pConfId']."_".$patientHeaderID.'.jpg';
	$ascemrDrawingContent = file_get_contents("html2pdf/white.jpg");
	$imwAscemrDrawingPath = $imwAscemrDrawingFolder."/".$imwAscemrImageName;
	file_put_contents($imwAscemrDrawingPath,$ascemrDrawingContent);
	$imwAscemrImgSrcPath = "/".$imwDirectoryName."/interface/main/uploaddir/PatientId_".$patientHeaderImwPatientId."/".$imwAscemrImageDir."/".$imwAscemrImageName;
	if(trim($imwPracticeName)) {
		$imwAscemrImgSrcPath = "../../data/".$imwPracticeName."/PatientId_".$patientHeaderImwPatientId."/".$imwAscemrImageDir."/".$imwAscemrImageName;
	}
	$headerImgLogo = '<img src="'.$imwAscemrImgSrcPath.'" width="'.$imgwidth.'" height="'.$imgheight.'">';
}

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
$headerBarTable = '';
if($headerBar=='yes') {
	$headerBarTable.='<page_header>

					<table align="center" style="width: 100%; "  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
						<tr>
							<td style="width:40%" class="tb_headingHeader">'.$patientHeaderName.'</td>
							<td style="width:30%" class="tb_headingHeader">';
								$headerBarTable.= $patientHeadersex;
								if($patientHeader_age){$headerBarTable.='('.$patientHeader_age.' Yr)&nbsp;'.$patientHeader_dob;}
			$headerBarTable.='  </td>';
			$headerBarTable.='  <td style="width:30%; text-align:right" class="tb_headingHeader">Surgery Date:&nbsp;'.$Confirm_patientHeaderDos.' </td>
						</tr>
					</table>
				</page_header>';
}
$page_footer_html = '';
if( $enableFooterPaging ) {
	$page_footer_html .= '
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>';
	
}
$head_table.=$page_footer_html;
$head_table.=$headerBarTable;
if($fromFile == "operative_recordPdf"){	
	$head_table.='<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
				<tr height="30" >
					<td valign="top"  align="left" ><b>'.$name.'<br>'.$address.'</b></td>
					<td valign="top"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$headerImgLogo.'</td>
				</tr>
				<tr><td valign="top" colspan="2" height="3">&nbsp;</td></tr>
				<tr><td valign="top" colspan="2" bgcolor="#000000" height="3">&nbsp;</td></tr>
				<tr><td valign="top" colspan="2" height="3">&nbsp;</td></tr>
				
			</table>'."\n";
}
else{
	$head_table.='<table align="center" style="width: 100%; "  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
				<tr height="30" >
					<td valign="top"  align="left" ><b>'.$name.'<br>'.$address.'</b></td>
					<td valign="top"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$headerImgLogo.'</td>
				</tr>
				<tr><td valign="top" colspan="2" style="width: 100%; " height="1"></td></tr>
				<tr><td valign="top" colspan="2" style="width: 100%; " bgcolor="#000000" height="1"></td></tr>
				<tr><td valign="top" colspan="2" style="width: 100%; " height="1"></td></tr>
				
			</table>'."\n";	
	$head_table.='
	<table style="width:700px; font-size:14px;" cellpadding="0" cellspacing="0">
		<tr>
			<td style="width:140px; font-weight:bold;text-align:right; padding-top:5px;">Patient Name:&nbsp;</td>
			<td style="width:200px; padding-top:5px; ">'.$patientHeaderName.'</td>
			<td style="width:80px;  padding-top:5px;font-weight:bold;text-align:right; ">ASC:&nbsp;</td>
			<td style="width:90px; padding-top:5px;">'.$Confirm_patientHeaderAscID.'</td>
			<td style="width:100px;  padding-top:5px;font-weight:bold;text-align:right; ">Surgeon:&nbsp;</td>
			<td style="width:190px;  padding-top:5px;">'.$Confirm_patientHeaderSurgeon_name.'</td>
		</tr>
		<tr>
			<td style="width:140px;  padding-top:5px;font-weight:bold;text-align:right;">Address:&nbsp;</td>
			<td style="width:200px; padding-top:5px;vertical-align:top; " rowspan="2">';
			if(trim($patientHeaderstreet1)){
				$head_table.= $patientHeaderstreet1.'<br>';
			}
			$head_table.= $patientHeadercity;
			if($patientHeaderstate){
				$head_table.=', '.$patientHeaderstate.' '.$patientHeaderzip;
			}
			$head_table.='</td>
			<td style="width:20px;  padding-top:2px;font-weight:bold;text-align:right; ">DOB:&nbsp;</td>
			<td style="width:90px; padding-top:2px;">'.$patientHeader_dob.'</td>
			<td style="width:100px;  padding-top:2px;font-weight:bold;text-align:right; ">Surgery Date:&nbsp;</td>
			<td style="width:190px;  padding-top:2px;">'.$Confirm_patientHeaderDos.'</td>
		</tr>
		<tr>
			<td style="width:140px;  padding-top:2px;font-weight:bold;text-align:right;">&nbsp;</td>
			<td style="width:20px;  padding-top:2px;font-weight:bold;text-align:right;  vertical-align:top;">Age:&nbsp;</td>
			<td style="width:90px; padding-top:2px; vertical-align:top;">';
				if($patientHeader_age){
					$head_table.=$patientHeader_age.' yrs';
				}
			$head_table.='</td>
			<td style="width:100px;  padding-top:2px;font-weight:bold;text-align:right;  vertical-align:top;">Site:&nbsp;</td>
			<td style="width:190px;   padding-top:2px;vertical-align:top;">'.$Confirm_patientHeaderSite.'</td>
		</tr>
		<tr>
			<td style="width:140px;  padding-top:2px;font-weight:bold;text-align:right;" valign="top">Tel.&nbsp;(H):&nbsp;</td>
			<td style="width:200px;  padding-top:2px;">'.$patientHeaderhomePhone.'</td>
			<td style="width:20px;  padding-top:2px;font-weight:bold;text-align:right; ">Sex:&nbsp;</td>
			<td style="width:90px; padding-top:2px;">'.$patientHeadersex.'</td>
			<td style="width:100px; padding-top:2px; font-weight:bold;text-align:right; ">Allergies:&nbsp;</td>
			<td style="width:190px; padding-top:2px; ">'.$allergiesValue.'</td>
		</tr>
		 <tr>
		 	<td style="width:140px;padding-top:2px; vertical-align:top;font-weight:bold;text-align:right; white-space:nowrap;">Tel.&nbsp;(W):&nbsp;</td>
		 	<td style="width:200px;padding-top:2px; vertical-align:top;">'.$patientHeaderworkPhone.'</td>	
			<td style="width:20px;padding-top:2px;vertical-align:top;font-weight:bold;text-align:right; ">A/D:&nbsp;</td>
			<td style="width:90px;padding-top:2px;vertical-align:top;">'.$Confirm_patientHeaderAdvanceDirective.'</td>
			<td colspan="2" style="width:190px;vertical-align:top;padding-top:2px;"><b>Assist. by Translator:</b>&nbsp;'.$Confirm_patientHeaderAssist_by_translator.'</td>
	
		</tr>
		<tr>
		 	<td style="width:140px;padding-top:2px; vertical-align:top;font-weight:bold;text-align:right; white-space:nowrap;">Anesthesia&nbsp;Provider:&nbsp;</td>
			<td style="width:200px;padding-top:2px; vertical-align:top;">'.$Confirm_patientHeaderAnesthesiologist_name_print.'</td>
			<td style="width:20px;padding-top:2px;vertical-align:top;font-weight:bold;text-align:right; "></td>
			<td style="width:90px;padding-top:2px;vertical-align:top;"></td>
			<td colspan="2" style="width:190px;vertical-align:top;padding-top:2px;"></td>
	
		</tr>

		';
	if(trim($Confirm_patientHeaderPrimProc)){
		$head_table.='
		<tr>
			<td style="width:140px; padding-top:2px;font-weight:bold;text-align:right;vertical-align:top;">Primary&nbsp;Procedure:&nbsp;</td>
			<td colspan="5" style="width:600px; padding-top:2px;vertical-align:top;">'.$Confirm_patientHeaderPrimProc.'</td>
		</tr>';
	}
	if((trim($Confirm_patientHeaderSecProc)!="N/A") && ($Confirm_patientHeaderSecProc)){
		$head_table.='
		<tr>
			<td style="width:140px; padding-top:2px;font-weight:bold;text-align:right;vertical-align:top;">Secondary&nbsp;Procedure:&nbsp;</td>
			<td colspan="5" style="width:600px; padding-top:2px;vertical-align:top;">'.$Confirm_patientHeaderSecProc.'</td>
		</tr>';
	}
$head_table.='
	</table>';
	$head_table.='<table style="width:700px;margin:5px 20px 0 0; border:1px solid #000;font-size:12px;" cellpadding="0" cellspacing="0">
	<tr>
    	<td style="width:75px; padding-left:5px; height:20px; font-weight:bold;">Base Line<br>Vital Signs</td>
        <td style="width:75px; padding-left:3px; height:20px;"><b>B/P</b>&nbsp;'.$vitalSignBp_NursingHeader.'</td>
        <td style="width:45px; padding-left:3px; height:20px;"><b>P</b>&nbsp;'.$vitalSignP_NursingHeader.'</td>
        <td style="width:45px; padding-left:3px; height:20px;"><b>R</b>&nbsp;'.$vitalSignR_NursingHeader.'</td>
        <td style="width:80px; padding-left:3px; height:20px;"><b>O2SAT</b>&nbsp;'.$vitalSignO2SAT_NursingHeader.'</td>
        <td style="width:100px; padding-left:3px; height:20px;"><b>Temp</b>&nbsp;'.$vitalSignTemp_NursingHeader.'</td>
		<td style="width:65px; padding-left:3px; height:20px;"><b>Height</b>&nbsp;'.$vitalSignHeight_NursingHeader.'</td>
		<td style="width:85px; padding-left:3px; height:20px;"><b>Weight</b>&nbsp;'.$vitalSignWeight_NursingHeader.'</td>
		<td style="width:57px; padding-left:3px; height:20px;"><b>BMI</b>&nbsp;'.$vitalSignBmi_NursingHeader.'</td>
		<td style="width:50px; padding-left:3px; height:20px;"><b>BGL</b>&nbsp;'.$vitalSignBgl_NursingHeader.'</td>
	</tr>
</table>';
}
$tableCss='
	<style type="text/css">
		table{
			font-size:14px;
		}
		.fheader{
			padding:5px 0px 5px 0px;
			font-weight:bold;
			font-size:16px;
			text-decoration:underline;
			text-align:center;
		}
		.fl{
			font-size:25px;
		}
		.fm{
			font-size:20px;
		}
		.bold{
			font-weight:bold;
		}
		.pt5{
			padding-top:5px;	
		}
		.pd{
			padding:4px;	
		}
		.pl5{
			padding-left:5px;
		}
		.pr5{
			padding-right:5px;
		}
		.bgcolor{
			background:#C0C0C0;
		}
		.cbold{
			text-align:center;
			font-weight:bold;		
		}
		.txt_rgt{
			text-align:right;	
		}
		.bdrbtm{
			border-bottom:1px solid #C0C0C0;
			height:20px;	
			vertical-align:top;
		}
		.bdrtop{
			border-top:1px solid #C0C0C0;
			height:20px;	
		}
		.bdrrght{
			border-right:1px solid #C0C0C0;
			height:20px;
			vertical-align:top;
		}
		.bdrbtm_new{
			border-bottom:1px solid #C0C0C0;
			vertical-align:top;
		}
		.bdrrght_new{
			border-right:1px solid #C0C0C0;
			vertical-align:top;
		}
		.bdrBtmRght{
			border-bottom:1px solid #C0C0C0;
			border-right:1px solid #C0C0C0;
			vertical-align:top;
		}
		.tb_headingHeader{
			font-weight:bold;
			color:#FFFFFF;
			background-color:#4684ab;
		}
	</style>';
$head_table.=$tableCss;
?>