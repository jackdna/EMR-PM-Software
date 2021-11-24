<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
$head_table='';
$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes= imw_query($SurgeryQry);
$SurgeryRecord=imw_fetch_array($SurgeryRes);
$name= $SurgeryRecord['name'];
$address= $SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state'];
$img = $SurgeryRecord['logoName'];
$patientHeaderInfo = $objManageData->getRowRecord('patient_data_tbl', 'patient_id', $patient_id);
$patientHeaderFname = $patientHeaderInfo->patient_fname;
$patientHeaderMname = $patientHeaderInfo->patient_mname;
$patientHeaderNname = $patientHeaderInfo->patient_lname;
$patientHeaderName = $patientHeaderFname." ".$patientHeaderMname." ".$patientHeaderNname;
	
$patientHeaderstreet1 = stripslashes($patientHeaderInfo->street1);	
$patientHeaderstreet2 = stripslashes($patientHeaderInfo->street2);	
$patientHeadercity = $patientHeaderInfo->city;	
$patientHeaderstate = $patientHeaderInfo->state;	
$patientHeaderzip = $patientHeaderInfo->zip;	
$patientHeaderdate_of_birth = $patientHeaderInfo->date_of_birth;	
		$patientHeader_dob_split = explode("-",$patientHeaderdate_of_birth);
		$patientHeader_dob = $patientHeader_dob_split[1]."-".$patientHeader_dob_split[2]."-".$patientHeader_dob_split[0];

	//CODE TO CALCULATE AGE OF PATIENT
	if($patientHeaderInfo->date_of_birth!="" && $patientHeaderInfo->date_of_birth!="0000-00-00"){
		$tmpHeader_date = $patientHeaderInfo->date_of_birth;
		list($yearHeader, $monthHeader, $dayHeader) = explode('-',$tmpHeader_date);
			
			$create_dateHeader = $monthHeader."-".$dayHeader."-".$yearHeader;
			
			$dformatHeader="-";
			$patientHeader_age=round(dateDiffHeader($dformatHeader,date("m-d-Y", time()), $create_dateHeader)/365, 0);
			if(date("m")<$month){
			$patientHeader_age=$patientHeader_age-1;
			}
	}else{
		$create_dateHeader="";
	}	
	//END CODE TO CALCULATE AGE OF PATIENT


$patientHeadersexTemp = $patientHeaderInfo->sex;
	if($patientHeadersexTemp=="m") { $patientHeadersex = "Male";} else { $patientHeadersex = "Female";  }	
$patientHeaderhomePhone = $patientHeaderInfo->homePhone;	
$patientHeaderworkPhone = $patientHeaderInfo->workPhone;
	if($patientHeaderhomePhone<>"") { $patientHeaderPhone =  $patientHeaderhomePhone;}else { $patientHeaderPhone = $patientHeaderworkPhone; }


$patientHeaderID = $patientHeaderInfo->patient_id;

$Confirm_patientHeaderInfo = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfId);
$Confirm_patientHeaderAscID = $Confirm_patientHeaderInfo->ascId;
$Confirm_patientHeaderDosTemp = $Confirm_patientHeaderInfo->dos;
		$Confirm_patientHeaderDos_split = explode("-",$Confirm_patientHeaderDosTemp);
		$Confirm_patientHeaderDos = $Confirm_patientHeaderDos_split[1]."-".$Confirm_patientHeaderDos_split[2]."-".$Confirm_patientHeaderDos_split[0];
$Confirm_patientHeaderSurgeon_name = $Confirm_patientHeaderInfo->surgeon_name;
$Confirm_patientHeaderAnesthesiologist_name = $Confirm_patientHeaderInfo->anesthesiologist_name;

$Confirm_patientHeaderSiteTemp = $Confirm_patientHeaderInfo->site;
	// APPLYING NUMBERS TO PATIENT SITE
		if($Confirm_patientHeaderSiteTemp == 1) {
			$Confirm_patientHeaderSite = "Left Eye";  //OD
		}else if($Confirm_patientHeaderSiteTemp == 2) {
			$Confirm_patientHeaderSite = "Right Eye";  //OS
		}else if($Confirm_patientHeaderSiteTemp == 3) {
			$Confirm_patientHeaderSite = "Both Eye";  //OU
		}
	// END APPLYING NUMBERS TO PATIENT SITE

$Confirm_patientHeaderPrimProc = $Confirm_patientHeaderInfo->patient_primary_procedure;
if(strlen($Confirm_patientHeaderPrimProc)>20){ $Confirm_patientHeaderPrimProc = substr($Confirm_patientHeaderPrimProc,0,20)."... ";}
$Confirm_patientHeaderSecProc = $Confirm_patientHeaderInfo->patient_secondary_procedure;
if(strlen($Confirm_patientHeaderSecProc)>10){ $Confirm_patientHeaderSecProc = substr($Confirm_patientHeaderSecProc,0,10)."... ";}
$Confirm_patientHeaderAssist_by_translator = $Confirm_patientHeaderInfo->assist_by_translator;

//SET ALLERGIES VALUE
	$patient_allergies_tblQry = "SELECT * FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '".$pConfId."'";
	$patient_allergies_tblRes = imw_query($patient_allergies_tblQry) or die(imw_error());
	$patient_allergies_tblNumRow = imw_num_rows($patient_allergies_tblRes);
	if($patient_allergies_tblNumRow>0) {
		//$allergiesValue = '<img src="images/Interface_red_image003.gif" width="17" height="15" align="middle" onclick="showAllergiesPopUpFn();">';
		$allergiesValue = 'Has Allergies';
	}else {
		$allergiesValue = 'NKDA';
	}
//END SET ALLERGIES VALUE

//GET BASE LINE VITAL SIGN FROM PREOP NURSING RECORD
	$selectPreOpNursingHeaderQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '".$pConfId."'";
	$selectPreOpNursingHeaderRes = imw_query($selectPreOpNursingHeaderQry) or die(imw_error());
	$selectPreOpNursingHeaderNumRow = imw_num_rows($selectPreOpNursingHeaderRes);
	if($selectPreOpNursingHeaderNumRow>0) {
		$selectPreOpNursingHeaderRow = imw_fetch_array($selectPreOpNursingHeaderRes);
		$vitalSignBp_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignBp"];
		$vitalSignP_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignP"];
		$vitalSignR_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignR"];
		$vitalSignTemp_NursingHeader = $selectPreOpNursingHeaderRow["vitalSignTemp"];
		if($vitalSignTemp_NursingHeader<>"") {
			$vitalSignTemp_NursingHeader = $vitalSignTemp_NursingHeader;
		}
	}	
//END GET BASE LINE VITAL SIGN FROM PREOP NURSING RECORD
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
 $file=fopen('html2pdf/white.jpg','w+');
		fputs($file,$surgeryCenterLogo);
	$size=getimagesize('html2pdf/white.jpg');
	
	 $hig=$size[1];
	 $wid=$size[0];
//Set size for logo image
$filename='html2pdf/white.jpg';
 
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
 $tdheight= '20';
 
}
else
{
 $tdheight= '40';

}

	$head_table.='<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
				<tr height="30" >
					<td  align="left"   valign="bottom" ><b>'.$name.'<br>'.$address.'</b></td>
					<td  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
				</tr>
				<tr><td colspan="2" height="3">&nbsp;</td></tr>
				<tr><td colspan="2" bgcolor="#000000" height="3">&nbsp;</td></tr>
				<tr><td colspan="2" height="3">&nbsp;</td></tr>
				
			</table>'."\n";
	$head_table.='<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" >
					<tr>
						<td height="15" align="left" width="13%"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Patient Name</strong></font></td>
						<td height="15" align="left" width="18%"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1">'.$patientHeaderName.'</font></td>
						<td height="15" width="19%"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>ASC</strong>&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderAscID.'</font></td>
						<td height="15" width="25%"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Surgeon</strong>&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderSurgeon_name.'</font></td>
						<td height="15" width="25%"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Surgery Date&nbsp; </strong>&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderDos.'</font></td>
					</tr>
					<tr>
						<td height="'.$tdheight.'" ><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Address:</strong>&nbsp;&nbsp;&nbsp;</font></td>
						<td >'.$patientHeaderstreet1.'</td>
						
						<td  ><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>DOB</strong>&nbsp;&nbsp;&nbsp;'.$patientHeader_dob.'</font></td>
						<td  ><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Age</strong>&nbsp;&nbsp;&nbsp;'.$patientHeader_age.'yrs</font></td>
						<td ><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Sex</strong>&nbsp;&nbsp;&nbsp;'.$patientHeadersex.'</font></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td  height="'.$td.'"  align="left" ><font face="Verdana, Arial, Helvetica, sans-serif" size="+1">&nbsp;&nbsp;'.$Ptcompleteaddress.'</font></td>
						<td><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Tel.</strong>&nbsp;&nbsp;&nbsp;'.$patientHeaderPhone.'</font></td>
						<td><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Allergies&nbsp;</strong>&nbsp;&nbsp;&nbsp;'.$allergiesValue.'</font></td>
						<td><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Site</strong>&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderSite.'</font></td>
					</tr>
					<tr>
						<td  colspan="3" height="15"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Anesthesiologist&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderAnesthesiologist_name.'</font></td>
						<td  colspan="2" height="15"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Assisted by Translator&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderAssist_by_translator.'</font></td>
					</tr>
					<tr>
						<td  colspan="3" height="15"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Primary Proc.&nbsp;&nbsp;&nbsp;&nbsp;</strong> &nbsp;&nbsp;&nbsp;&nbsp;'.$Confirm_patientHeaderPrimProc.'</font></td>
						<td  colspan="2" height="15"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1"><strong>Secondary Proc.&nbsp;&nbsp;&nbsp;&nbsp;</strong> &nbsp;&nbsp;'.$Confirm_patientHeaderSecProc.'</font></td>
					</tr>
				</table>';
	$head_table.='<table cellpadding="0" cellspacing="0" align="center" width="100%" border="1">
					<tr>
						<td  height="15" colspan="2"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1">Base Line Vital Signs</font></td>
						<td  height="15"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1">B/P&nbsp;&nbsp;&nbsp;&nbsp;'.$vitalSignBp_NursingHeader.'</font></td>
						<td  height="15"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1">P&nbsp;&nbsp;&nbsp;'.$vitalSignP_NursingHeader.'</font></td>
						<td  height="15"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1">R&nbsp;&nbsp;&nbsp;'.$vitalSignR_NursingHeader.'</font></td>
						<td  height="15"><font face="Verdana, Arial, Helvetica, sans-serif" size="+1">Temp&nbsp;&nbsp;&nbsp;'.$vitalSignTemp_NursingHeader.'</font></td>
					</tr>
				 </table>';
?>