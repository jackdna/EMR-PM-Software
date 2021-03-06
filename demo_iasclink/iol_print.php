<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$loginUser = $_SESSION['iolink_loginUserId'];
$reqUserId=$_REQUEST['reqUserId'];
$selDos=$_REQUEST['selDos'];
$wt_order=$_REQUEST['wt_order'];
$show_canceled=$_REQUEST['show_canceled'];

if(!$selDos) {
	$selDos=date('Y-m-d');
}

//START GET SURGERYCENTER NAME
$getSurgeryCenterDetail = $objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', 1);
if($getSurgeryCenterDetail) {
	$surgerycenterName = $getSurgeryCenterDetail->name;
}

if($_SESSION['iolink_iasc_facility_id']) {
	$queryFac=imw_query("select * from facility_tbl where fac_id='".$_SESSION["iolink_facility_id"]."'") or die(imw_error());
	$dataFac=imw_fetch_object($queryFac);
	$surgerycenterName=stripcslashes($dataFac->fac_name);
	//$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);
}
//END GET SURGERYCENTER NAME

include("common/link_new_file.php");
include("common/iOLinkCommonFunction.php");
$practiceName = getPracticeName($loginUser,'Coordinator');
$coordinatorType = getCoordinatorType($loginUser);


$dayName = date("l",strtotime($selDos));
$showDos = date("m/d/y",strtotime($selDos));

$andStubInfoQry=='';
if($reqUserId) {
	// GETTING LOGIN USER NAME 
	$bookingSrgnNameQry = "SELECT fname, mname, lname FROM users WHERE usersId = '".$reqUserId."'";
	$bookingSrgnNameRes = imw_query($bookingSrgnNameQry);
	$bookingSrgnNameRow = imw_fetch_array($bookingSrgnNameRes);
	$bookingSrgnFName = stripslashes($bookingSrgnNameRow['fname']);
	$bookingSrgnMName = stripslashes($bookingSrgnNameRow['mname']);
	$bookingSrgnLName = stripslashes($bookingSrgnNameRow['lname']);
	
	if($bookingSrgnLName && $bookingSrgnFName) {
		$bookingSrgnName = $bookingSrgnLName.', '.$bookingSrgnFName.' '.$bookingSrgnMName;
	}
	// GETTING LOGIN USER NAME
	
	$andStubInfoQry = " AND patient_in_waiting_tbl.surgeon_fname='".addslashes($bookingSrgnFName)."' 
						AND patient_in_waiting_tbl.surgeon_mname='".addslashes($bookingSrgnMName)."' 
						AND patient_in_waiting_tbl.surgeon_lname='".addslashes($bookingSrgnLName)."' 
						AND users.usersId= '".$reqUserId."'
						AND patient_in_waiting_tbl.surgeon_fname=users.fname 
						AND patient_in_waiting_tbl.surgeon_mname=users.mname 
						AND patient_in_waiting_tbl.surgeon_lname=users.lname";
	
	$andStubInfoSubQry = "AND patient_in_waiting_tbl.surgeon_fname=users.fname 
						AND patient_in_waiting_tbl.surgeon_mname=users.mname 
						AND patient_in_waiting_tbl.surgeon_lname=users.lname";
	
}else {
	
	$AndUserPracticeNameQry="";
	if($coordinatorType!='Master') {
		$AndUserPracticeNameQry = getPracticeUser($practiceName,"AND","users");
		//$AndUserPracticeNameQry = " AND users.practiceName='".addslashes($practiceName)."'";
	}
	$andStubInfoQry = " $AndUserPracticeNameQry 
						AND patient_in_waiting_tbl.surgeon_fname=users.fname 
						AND patient_in_waiting_tbl.surgeon_mname=users.mname 
						AND patient_in_waiting_tbl.surgeon_lname=users.lname";

	$andStubInfoSubQry = " $AndUserPracticeNameQry 
						AND patient_in_waiting_tbl.surgeon_fname=users.fname 
						AND patient_in_waiting_tbl.surgeon_mname=users.mname 
						AND patient_in_waiting_tbl.surgeon_lname=users.lname";
}
$andSchCancelQry = " AND patient_in_waiting_tbl.dos='".$selDos."' AND patient_in_waiting_tbl.patient_status!='Canceled' ";
$andSchCanceledSubQry = " AND patient_in_waiting_tbl.dos='".$selDos."' AND patient_in_waiting_tbl.patient_status!='Canceled' ";
if($show_canceled == "yes") {
	$andSchCancelQry = " AND patient_in_waiting_tbl.patient_status='Canceled' ";
	$andSchCanceledSubQry = " AND patient_in_waiting_tbl.patient_status='Canceled' ";	
}

$facQry = "";
if(trim($_SESSION['iolink_iasc_facility_id'])) {
	$facQry = " AND patient_in_waiting_tbl.iasc_facility_id IN (".$_SESSION['iolink_iasc_facility_id'].") ";	
}
//START TO GET PATIENTS FROM patient_in_waiting_tbl
	$getStubInfoQry 	= "SELECT patient_in_waiting_tbl.*,users.usersId FROM patient_in_waiting_tbl,users WHERE users.deleteStatus!='Yes' $andSchCancelQry $andStubInfoQry $facQry  ORDER BY patient_in_waiting_tbl.surgeon_fname ASC "; //order by patient_in_waiting_tbl.surgery_time
	
	//$getStubInfoQry = "SELECT patient_in_waiting_tbl.*,users.usersId FROM patient_in_waiting_tbl,users WHERE patient_in_waiting_tbl.dos='".$selDos."' AND patient_in_waiting_tbl.patient_status!='Canceled' $andStubInfoQry  ORDER BY patient_in_waiting_tbl.surgery_time ASC ";
	$getStubInfoRes = imw_query($getStubInfoQry) or die($getStubInfoQry.imw_error());
	$getStubInfoNumRow = imw_num_rows($getStubInfoRes);
//END TO GET PATIENTS FROM patient_in_waiting_tbl

?>
 <?php 
 $table_pdf ='<style>
		.tb_heading{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000000;
			background-color:#FE8944;
		}
		.text_b{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			font-weight:bold;
			color:#000000;
		}
		.text{
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#FFFFFF;
		}
		.lightBlue {
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#EAF4FD;
		}
		.midBlue {
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#80AFEF;
		}
		.text_orangeb{
			font-weight:bold;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#FFFFFF;
			color:#CB6B43;
		}
		.lightGreen {
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#ECF1EA;
		}
		.lightorange {
			font-size:12px;
			font-family:Arial, Helvetica, sans-serif;
			background-color:#CB6B43;
		}
		
	</style>
	<page backtop="1mm" backbottom="1mm">';
	if($bookingSrgnName) { $bookingSrgnNameShow = $bookingSrgnName.' - '; }
	$dayNameTmp = $dayName;
	$showDosTmp = $showDos;
	if($show_canceled=="yes") {
		$bookingSrgnNameShow =  "Cancelled Appointment(s)";
		$dayNameTmp = "";
		$showDosTmp	= "";
	}
 $table_pdf.='
 	
	';
		if($getStubInfoNumRow>0) {
			$stub_tbl_groupTemp = array();
			$cntr=0;
			$wtOrderByQry = " patient_in_waiting_tbl.surgery_time ASC ";
			if($wt_order=="lname_asc") { 
				$wtOrderByQry = " patient_data_tbl.patient_lname ASC, patient_data_tbl.patient_fname ASC "; 
			}else if($wt_order=="lname_desc") {
				$wtOrderByQry = " patient_data_tbl.patient_lname DESC, patient_data_tbl.patient_fname DESC "; 
			}
			while($stub_tbl_group_row = imw_fetch_array($getStubInfoRes)) {
				$cntr++;
				$stub_surgeon_name = "";
				$stub_tbl_group_surgeon_name = "";
				$stub_tbl_group_surgeon_fname = trim(stripslashes($stub_tbl_group_row['surgeon_fname']));
				$stub_tbl_group_surgeon_mname = trim(stripslashes($stub_tbl_group_row['surgeon_mname']));
				$stub_tbl_group_surgeon_lname = trim(stripslashes($stub_tbl_group_row['surgeon_lname']));
				$stub_surgeon_name = trim(stripslashes($stub_tbl_group_row['surgeon_fname'])).' '.trim(stripslashes($stub_tbl_group_row['surgeon_mname'])).' '.trim(stripslashes($stub_tbl_group_row['surgeon_lname']));
				$user_tbl_group_surgeon_id = $stub_tbl_group_row['usersId'];
			
				if(!in_array($stub_surgeon_name,$stub_tbl_groupTemp)) {
					$stub_tbl_query = "SELECT patient_in_waiting_tbl.*,DATE_FORMAT( patient_in_waiting_tbl.surgery_time, '%h:%i %p' ) AS pt_surgery_time, users.usersId,DATE_FORMAT(patient_in_waiting_tbl.dos,'%m/%d/%Y') as patient_waiting_dos  
										FROM patient_in_waiting_tbl,users,patient_data_tbl  
										WHERE users.deleteStatus!='Yes'
										AND patient_in_waiting_tbl.surgeon_fname='".addslashes($stub_tbl_group_surgeon_fname)."' 
										AND patient_in_waiting_tbl.surgeon_mname='".addslashes($stub_tbl_group_surgeon_mname)."' 
										AND patient_in_waiting_tbl.surgeon_lname='".addslashes($stub_tbl_group_surgeon_lname)."' 
										AND patient_in_waiting_tbl.patient_id=patient_data_tbl.patient_id
										$andSchCanceledSubQry
										$andStubInfoSubQry  
										$facQry
										ORDER BY $wtOrderByQry ";
					
					
					$stub_tbl_res = imw_query($stub_tbl_query) or die($stub_tbl_query.imw_error());
					$stub_tbl_num_row=imw_num_rows($stub_tbl_res);

					if($cntr>1) {
						$table_pdf.='</page><page>';		
					}
					$table_pdf.='
								<table width="100%" border="0" >
									<tr>
										<td width="300">&nbsp;&nbsp;</td>
										<td align="center" class="text_orangeb">'.$surgerycenterName.'</td>
									</tr>
								</table>
								<table width="100%" border="0" >
									<tr>
										<td width="280">&nbsp;&nbsp;</td>
										<td align="center"><b>Booking Sheet</b></td>
										<td width="5">&nbsp;&nbsp;</td>
										<td align="center"><b>'.$bookingSrgnNameShow.'</b></td>
										<td align="center"><b>'.$dayNameTmp.'</b></td>
										<td align="center"><b>'.$showDosTmp.'</b></td>
									</tr>
								</table>';
									
					$table_pdf.='							
					<table  border="0" cellpadding="1" cellspacing="4" width="100%">
					<tr valign="middle" height="10"   >
						<td class="" colspan="7"  style=" padding-left:5px; " nowrap>Surgeon   &nbsp;<b>Dr. '.$stub_surgeon_name.' ('.$stub_tbl_num_row.')</b></td>
					</tr> 
					<tr valign="top" height="10"  class="text_b">
						<td width="155" class="midBlue" nowrap>Pt. Name - Detail</td>
						<td width="80"  class="midBlue">Procedure</td>
						<td width="70" class="midBlue">Comments</td>
						<td width="50" class="midBlue">S.Time</td>
						<td width="90" class="midBlue">IOL Man(Brand)</td>
						<td width="110" class="midBlue">IOL Model</td>
						<td width="80" class="midBlue">IOL Diopter</td>
					</tr>';
					$stub_tbl_groupTemp[] = $stub_surgeon_name;
					$r=0;
					while($getStubInfoRow = imw_fetch_array($stub_tbl_res)) {
						$r++;
						$bookBgTrColor="#FFFFFF";
						if($r%2==0) { $bookBgTrColor = "#F3F8F2";}
						$userTblUsersId = $getStubInfoRow['usersId'];
						$patient_waiting_patient_primary_procedure 		= $getStubInfoRow['patient_primary_procedure'];
						$patient_waiting_patient_secondary_procedure 	= $getStubInfoRow['patient_secondary_procedure'];
						$patient_waiting_patient_tertiary_procedure 	= $getStubInfoRow['patient_tertiary_procedure'];
						$iAscSyncroStatus = $getStubInfoRow['iAscSyncroStatus'];
						//$iolinkSyncroStatus = $getStubInfoRow['iolinkSyncroStatus'];
						$getProcedureIdQry 	= "SELECT * FROM procedures WHERE name != '' AND procedureAlias != '' AND (name = '$patient_waiting_patient_primary_procedure' OR procedureAlias = '$patient_waiting_patient_primary_procedure')";
						$getProcedureIdRes 	= imw_query($getProcedureIdQry);
						if(imw_num_rows($getProcedureIdRes)>0){
							$getProcedureIdRow = imw_fetch_array($getProcedureIdRes);
							$patient_primary_procedure_id = $getProcedureIdRow['procedureId'];
						}
						//GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
							if($userTblUsersId<>"") {
								$selectSurgeonQry 			= "select * from surgeonprofile where surgeonId = '$userTblUsersId'";
								$selectSurgeonRes 			= imw_query($selectSurgeonQry) or die(imw_error());
								while($selectSurgeonRow 	= imw_fetch_array($selectSurgeonRes)) {
									$surgeonProfileIdArr[] 	= $selectSurgeonRow['surgeonProfileId'];
								}
								if(is_array($surgeonProfileIdArr)){
									$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr);
								}else {
									$surgeonProfileIdImplode = 0;
								}
								$selectSurgeonProcedureQry 		= "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
								$selectSurgeonProcedureRes 		= imw_query($selectSurgeonProcedureQry) or die(imw_error());
								$selectSurgeonProcedureNumRow 	= imw_num_rows($selectSurgeonProcedureRes);
								if($selectSurgeonProcedureNumRow>0) {
									while($selectSurgeonProcedureRow 	= imw_fetch_array($selectSurgeonProcedureRes)) {
										$surgeonProfileProcedureId 		= $selectSurgeonProcedureRow['procedureId'];
										if($patient_primary_procedure_id == $surgeonProfileProcedureId) {
											$consentTemplateFound = "true";
											$consentMultipleTemplateId = $selectSurgeonProcedureRow['consentTemplateId'];
										}		
									}
								}	
							}
							
							$consentMultipleTemplateIdArr = array();
							$consentMultipleTemplateIdArr = explode(',',$consentMultipleTemplateId);

						//END GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
						
						$patient_in_waiting_id 	= $getStubInfoRow['patient_in_waiting_id'];
						$patient_id 			= $getStubInfoRow['patient_id'];
						
						
						$hrefBookingStart 		= "<a class='link_home' style='cursor:pointer;' href='#' onClick='top.iframeHome.iOLinkPtDetailFrameId.waitingPatient_info(\"$patient_id\",\"$patient_in_waiting_id\");'>";
						$hrefBookingEnd			="</a>";
						
						$patient_in_waiting_dos_temp 	= $getStubInfoRow['dos'];
						$patient_in_waiting_dos_split 	= explode("-",$patient_in_waiting_dos_temp);
						$patient_in_waiting_dos 		= $patient_in_waiting_dos_split[1]."-".$patient_in_waiting_dos_split[2]."-".$patient_in_waiting_dos_split[0];
						
						//START PERSONAL DETAIL
						$patientDataTblQry 		= "SELECT * FROM `patient_data_tbl` WHERE patient_id='".$patient_id."' AND patient_id!=''";
						$patientDataTblRes 		= imw_query($patientDataTblQry) or die(imw_error()); 
						$patientDataTblNumRow 	= imw_num_rows($patientDataTblRes);
						
						$patientHomePhoneInfo='';
						$patientWorkPhoneInfo='';
						$patient_Gender='';
						if($patientDataTblNumRow>0) {
							$patientDataTblRow 	= imw_fetch_array($patientDataTblRes);
					
							$patient_first_name 	= $patientDataTblRow['patient_fname'];
							$patient_middle_name 	= $patientDataTblRow['patient_mname'];
							$patient_last_name 		= $patientDataTblRow['patient_lname'];
							$patient_name 			= $patient_last_name.", ".$patient_first_name;
						
							$patient_dob_temp 		= $patientDataTblRow['date_of_birth'];
							$patient_dob='';
							if($patient_dob_temp!=0) { $patient_dob = date('m/d/y',strtotime($patient_dob_temp)); }
						
							$patient_address1		= stripslashes(htmlentities($patientDataTblRow['street1']));
							$patient_sex 			= $patientDataTblRow['sex'];
							
							
							if($patient_sex=='m') {
								$patient_Gender='Male';
							}else if($patient_sex=='f') {
								$patient_Gender='Female';
							}
							
							$patient_city 			= $patientDataTblRow['city'];
							$patient_state 			= $patientDataTblRow['state'];
							$patient_zip 			= $patientDataTblRow['zip']; 
							$patient_homePhone 		= $patientDataTblRow['homePhone']; 
							$patient_workPhone 		= $patientDataTblRow['workPhone']; 
						
							if($patient_homePhone) {
								$patientHomePhoneInfo='H ???'.$patient_homePhone;
							}
							if($patient_workPhone) {
								$patientWorkPhoneInfo='W(or C) ???'.$patient_workPhone;
							}
						}
						//END PERSONAL DETAIL
						
						$patient_waiting_commentTemp= stripslashes(htmlentities($getStubInfoRow['comment']));
						$patient_waiting_comment 	= wordwrap($patient_waiting_commentTemp, 16, "\n", 1);
						
						$patient_waiting_site = $getStubInfoRow['site'];
						$patient_waiting_surgery_time = $getStubInfoRow['pt_surgery_time'];
						
						//START GET OPERATOR INITIAL
						$patient_waiting_operator_id = $getStubInfoRow['operator_id'];
						$operatorInitialsArr = array();
						$operatorInitialsArr = $objManageData->getOperatorInitialsArray();
						$operatorInitials=$operatorInitialsArr[$patient_waiting_operator_id];
						//END GET OPERATOR INITIAL
						
						//START APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
							if($patient_waiting_site=='left') {
								if($patient_waiting_patient_primary_procedure) {
									$patient_waiting_patient_primary_procedure = $patient_waiting_patient_primary_procedure.' (OS)';
								}
								if($patient_waiting_patient_secondary_procedure) {
									$patient_waiting_patient_secondary_procedure = $patient_waiting_patient_secondary_procedure.' (OS)';
								}
								if($patient_waiting_patient_tertiary_procedure) {
									$patient_waiting_patient_tertiary_procedure = $patient_waiting_patient_tertiary_procedure.' (OS)';
								}
							}else if($patient_waiting_site=='right') {
								if($patient_waiting_patient_primary_procedure) {
									$patient_waiting_patient_primary_procedure = $patient_waiting_patient_primary_procedure.' (OD)';
								}
								if($patient_waiting_patient_secondary_procedure) {
									$patient_waiting_patient_secondary_procedure = $patient_waiting_patient_secondary_procedure.' (OD)';
								}
								if($patient_waiting_patient_tertiary_procedure) {
									$patient_waiting_patient_tertiary_procedure = $patient_waiting_patient_tertiary_procedure.' (OD)';
								}
							}else if($patient_waiting_site=='both') {
								if($patient_waiting_patient_primary_procedure) {	
									$patient_waiting_patient_primary_procedure = $patient_waiting_patient_primary_procedure.' (OU)';
								}
								if($patient_waiting_patient_secondary_procedure) {
									$patient_waiting_patient_secondary_procedure = $patient_waiting_patient_secondary_procedure.' (OD)';
								}
								if($patient_waiting_patient_tertiary_procedure) {
									$patient_waiting_patient_tertiary_procedure = $patient_waiting_patient_tertiary_procedure.' (OD)';
								}
							}
						//END APPEND OS/OD/OU WITH PRIMARY PROCEDURE BASED ON SITE
						
						//GET AGE OF PATIENT
							list($temp_year,$temp_month,$temp_day) = explode('-',$patient_dob_temp);
							$dformatNew="-";
							$create_dateiOLink = $temp_month."-".$temp_day."-".$temp_year;
							$patient_ageNew=round(dateDiffCommon($dformatNew,date("m-d-Y", time()), $create_dateiOLink)/365, 0);
							if(date("m")<$temp_month || date("d")<$temp_day){
								$patient_ageNew=$patient_ageNew-1;
							}
						//END GET AGE OF PATIENT
						
						//START GET SCAN DETAIL
							$ekgImgSrc 			= "images/chk_off1.gif";
							$hPImgSrc 			= "images/chk_off1.gif";
							$healthQuestImgSrc 	= "images/chk_off1.gif";
							$ocularHxImgSrc 	= "images/chk_off1.gif";
							$EkgHpOcularHealthArr = array('ekg', 'h&p', 'healthQuest','ocularHx');
							foreach($EkgHpOcularHealthArr as $fldrNme) {
								unset($conditionArr);
								$conditionArr['patient_in_waiting_id'] = $patient_in_waiting_id;
								$conditionArr['patient_id'] = $patient_id;
								$conditionArr['iolink_scan_folder_name'] = $fldrNme;
								$consentHpEkgExistDetails = $objManageData->getMultiChkArrayRecords('iolink_scan_consent', $conditionArr);	
								if($consentHpEkgExistDetails) {
									if($fldrNme=='ekg') 		{ 	$ekgImgSrc 			= "images/check_mark16.gif"; }
									if($fldrNme=='h&p') 		{ 	$hPImgSrc 			= "images/check_mark16.gif"; }
									if($fldrNme=='healthQuest') { 	$healthQuestImgSrc 	= "images/check_mark16.gif"; }
									if($fldrNme=='ocularHx') 	{ 	$ocularHxImgSrc 	= "images/check_mark16.gif"; }
								}
							}
							//START CODE TO CHECK HEALTH-QUEST EXIST IN DATABASE
							$chkHealthQuestFormExistQry = "SELECT preOpHealthQuesId FROM iolink_preophealthquestionnaire 
															WHERE patient_in_waiting_id='".$patient_in_waiting_id."'
															AND (form_status='completed' OR form_status='not completed')";
							$chkHealthQuestFormExistRes = imw_query($chkHealthQuestFormExistQry) or die(imw_error());
							$chkHealthQuestFormExistNumRow = imw_num_rows($chkHealthQuestFormExistRes);
							if($chkHealthQuestFormExistNumRow>0) {
								$healthQuestImgSrc 	= "images/check_mark16.gif";
							}
							//END CODE TO CHECK HEALTH-QUEST EXIST IN DATABASE
						
						//END GET SCAN DETAIL									
						
						
						//START GETTING CONSENT FORM COLUMNS
							if(!$consentMultipleTemplateId) { $consentMultipleTemplateId=0;  }
							$selectConsentTemplateQry 		= "select * from consent_forms_template where consent_id in($consentMultipleTemplateId) order by consent_id";
							$selectConsentTemplateRes 		= imw_query($selectConsentTemplateQry) or die(imw_error());
							$selectConsentTemplateNumRow 	= imw_num_rows($selectConsentTemplateRes);
							$blIncompleteForms = false;
							if($selectConsentTemplateNumRow>0) {
								while($selectConsentTemplateRow = imw_fetch_array($selectConsentTemplateRes)) {
									$dispScnEFormStatus='';
									$dispEFormStatus='';
									$blStatus = "off";
									$selectConsentTemplateId = $selectConsentTemplateRow['consent_id'];
									//$showCheckMark = '<img src="images/chk_off1.gif" style=" cursor:pointer; " onClick="document.frmOpenConsentWin.hidd_scanDoneStatusId.value='."''".';document.frmOpenConsentWin.hidd_eFromDoneStatusId.value='."''".';opnConsentWindow('."'$patient_id'".','."'$patient_in_waiting_id'".','."'$selectConsentTemplateId'".','."'$consentMultipleTemplateId'".','."''".');">';
									if(!in_array($selectConsentTemplateId,$consentMultipleTemplateIdArr)) {
										//$showCheckMark = 'N/A';
										$blStatus = "na";
									}
									//START CONSENT E-FORM DETAIL
									unset($conditionArr);
									$conditionArr['fldPatientWaitingId'] = $patient_in_waiting_id;
									$conditionArr['consent_template_id'] = $selectConsentTemplateId;
									
									$consentEFormExistDetails = $objManageData->getMultiChkArrayRecords('iolink_consent_filled_form', $conditionArr);	
									
									if($consentEFormExistDetails) {
										foreach($consentEFormExistDetails as $consentEFormKey) {
											$consentEFormName =  $consentEFormKey->surgery_consent_name;
											$consentEFormSignedStatus =  $consentEFormKey->consentSignedStatus;
											if($consentEFormSignedStatus=='true') {
												$dispEFormStatus = 'EFormDone';
												//$showCheckMark = '<img src="images/check_mark16.png" style=" cursor:pointer; " onClick="document.frmOpenConsentWin.hidd_scanDoneStatusId.value='."'$dispScnStatus'".';document.frmOpenConsentWin.hidd_eFromDoneStatusId.value='."'$dispEFormStatus'".';opnConsentWindow('."'$patient_id'".','."'$patient_in_waiting_id'".','."'$selectConsentTemplateId'".','."'$consentMultipleTemplateId'".','."'opnDisplayWin'".');">';
												$blStatus = "on";
											}
										}
									}
									//END CONSENT E-FORM DETAIL
									
									//START CODE TO CHECK CONSENT FORM SIGNED VIA PDF OR NOT
									$chkIolinkScanConsentQry 	= "select * from iolink_scan_consent where patient_in_waiting_id='".$patient_in_waiting_id."' AND idoc_consent_template_id = '".$selectConsentTemplateId."' order by scan_consent_id";
									$chkIolinkScanConsentRes 	= imw_query($chkIolinkScanConsentQry) or die(imw_error());
									$chkIolinkScanConsentNumRow = imw_num_rows($chkIolinkScanConsentRes);
									if($chkIolinkScanConsentNumRow>0) {
										$blStatus = "on";	
									}
									//END CODE TO CHECK CONSENT FORM SIGNED VIA PDF OR NOT
									
									if($blIncompleteForms == false && $blStatus != "on"){
										$blIncompleteForms = true;
									}
								}
							}else {
								$blIncompleteForms 		= true;
							}
							//Start Medication Status	
							$selectCountMedication = "select count(*) from iolink_patient_prescription_medication WHERE patient_id = '$patient_id' and patient_in_waiting_id = '$patient_in_waiting_id' ";
							$selectConsentTemplateRes = imw_query($selectCountMedication) or die(imw_error());
							list($selectCountMedicationTot) = imw_fetch_array($selectConsentTemplateRes);
							if($selectCountMedicationTot){
								$medicationSatus = "Yes";
							}
							else{
								$medicationSatus = "None";
							}
							//$selectConsentTemplateNumRow = imw_num_rows($selectConsentTemplateRes);
						
						//End Medication Status	
						//Start Allergy Status	
							$allergyColor 				= '#669966';
							$queryGetAllergy 			= "SELECT allergy_name from iolink_patient_allergy WHERE patient_id = '$patient_id' and patient_in_waiting_id = '$patient_in_waiting_id'";
							$queryGetAllergyRes 		= imw_query($queryGetAllergy) or die(imw_error());
							$queryGetAllergyResNumRow 	= imw_num_rows($queryGetAllergyRes);
							if($queryGetAllergyResNumRow>0){
								
								$queryGetAllergyRow 	= imw_fetch_array($queryGetAllergyRes);
								$strPatientAllergyName 	= $queryGetAllergyRow['allergy_name'];
								if(trim($strPatientAllergyName)=='NKA' && $queryGetAllergyResNumRow==1) {
									$allergyStatus 		= 'NKA';
								}else {
									$allergyStatus 		= 'Allergy';
									$allergyColor 		= '#FF0000';
								}
							}
							else{
								$allergyStatus 			= '';
							}
						
						//End Allergy Status
						
						//START GET IOL MANUFACTURER VALUE	
							$iolManufacturerNameQry 	= "SELECT * FROM iolink_iol_manufacturer 
															WHERE patient_id = '".$patient_id."'
															AND patient_id != '' 
															AND patient_in_waiting_id = '".$patient_in_waiting_id."' 
															AND patient_in_waiting_id!= ''
															ORDER BY opRoomDefault DESC, iol_manufacturer_id DESC";
							$iolManufacturerNameRes 	= imw_query($iolManufacturerNameQry) or die(imw_error());
							$iolManufacturerNameNumRow 	= imw_num_rows($iolManufacturerNameRes);
							
						//END GET IOL MANUFACTURER VALUE
						
						//START TO SET STATUS FIELD
						if($iAscSyncroStatus=='Syncronized') {
							$showSyncroStatus =  "<img src='images/check_mark16.gif' />";
						}else {
							$showSyncroStatus = "<img src='images/chk_off1.gif' />";
						}
						//END TO SET STATUS FIELD
						
						//START SET IMAGE FOR CONSENT FORMS
						if ($blIncompleteForms == true){
							$consentFormsImageSrc = "images/chk_off1.gif";
						}else{
							$consentFormsImageSrc = "images/check_mark16.gif";
						}
						//END SET IMAGE FOR CONSENT FORMS
						
						//START GET PRIMARY SECONDARY INSURANCE INFORMATION
						$insArr = array('primary','secondary');
						$primarySSN			='';	$secondarySSN			='';
						$primaryInsProvider	='';	$secondaryInsProvider	='';
						$primaryPolicy		='';	$secondaryPolicy		='';
						$primaryInfo		='';	$secondaryInfo			='';
						foreach($insArr as $insPriSec) {
							$PriInsDataQry = "select * from insurance_data where  waiting_id = '".$patient_in_waiting_id."' and type = '".$insPriSec."'";
							$PriInsDataRes = imw_query($PriInsDataQry);
							$PriInsDataNumRow = imw_num_rows($PriInsDataRes);
							if($PriInsDataNumRow>0) {
								$PriInsDataRow = @imw_fetch_array($PriInsDataRes);
								if($insPriSec=='primary'){ 
									$primarySSN 		= $PriInsDataRow['ssn'];
									$primaryInsProvider = $PriInsDataRow['ins_provider'];
									$primaryPolicy 		= $PriInsDataRow['policy'];
									$primaryHifen='';
									if($primaryInsProvider && $primaryPolicy) {
										$primaryHifen=' - ';
									}
									$primaryInfo=$primaryInsProvider.$primaryHifen.$primaryPolicy;
								}else if($insPriSec=='secondary') {
									$secondarySSN 			= $PriInsDataRow['ssn'];
									$secondaryInsProvider 	= $PriInsDataRow['ins_provider'];
									$secondaryPolicy 		= $PriInsDataRow['policy'];
									$secondaryHifen='';
									if($secondaryInsProvider && $secondaryPolicy) {
										$secondaryHifen=' - ';
									}
									$secondaryInfo=$secondaryInsProvider.$secondaryHifen.$secondaryPolicy;
								}
							}	
						}	
						$primaryInfo=wordwrap($primaryInfo, 28, "<br>", 1);
						$secondaryInfo=wordwrap($secondaryInfo, 28, "<br>", 1);	
						//END GET PRIMARY SECONDARY INSURANCE INFORMATION
						

						
					?>
					<?php $table_pdf.='
				<tr valign="top" height="22" bgcolor="#000000">
					<td width="155" class="lightBlue" >'.$patient_name.' - '.$patient_dob.'</td>
					<td width="120" class="lightBlue">'.
					$patient_waiting_patient_primary_procedure.'<br />'.
					$patient_waiting_patient_secondary_procedure.'<br />'.
					$patient_waiting_patient_tertiary_procedure
					.'</td>
					<td width="90" align="left" class="lightBlue" >'.$patient_waiting_comment.'</td>
					<td width="50" align="left" class="lightBlue">'.$patient_waiting_surgery_time.'</td>
					<td width="280" colspan="3" class="lightBlue" >';
						$iolManufacturerName	= '';
						$iolModelName 			= '';
						$iolDiopterName 		= '';
						$iolManufacturerTr='';
						$iolModelTr='';
						$iolDiopterTr='';
						if($iolManufacturerNameNumRow>0) {
						$table_pdf.='	
							<table border="0" cellpadding="1" cellspacing="4" width="100%">';
								$iolManModelCntr = 0;
								
								while($iolManufacturerNameRow = imw_fetch_array($iolManufacturerNameRes)) {
									
									$iolManufacturerName	= trim(stripslashes($iolManufacturerNameRow['manufacture']));
									$iolLensBrand			= trim(nl2br(stripslashes($iolManufacturerNameRow['lensBrand'])));
									$iolModelName 			= trim(stripslashes($iolManufacturerNameRow['model']));
									$iolDiopterName 		= trim(stripslashes($iolManufacturerNameRow['Diopter']));
									
									$iolLensBrandShow='';
									if($iolLensBrand) { $iolLensBrandShow='('.$iolLensBrand.')'; }
									
									if($iolManufacturerName || $iolModelName || $iolDiopterName) {
										$iolManModelCntr++;
										$iolManBgColor='#FFFFFF';
										if($iolManModelCntr%2==0) { $iolManBgColor='#F1F1F1'; }
										
						$table_pdf.='			
										<tr>
											<td bgcolor="'.$iolManBgColor.'" width="90" class="lightBlue">'.$iolManufacturerName.$iolLensBrandShow.'</td>
											<td bgcolor="'.$iolManBgColor.'" width="110" class="lightBlue">'.$iolModelName.'</td>
											<td bgcolor="'.$iolManBgColor.'" width="80" class="lightBlue">'.$iolDiopterName.'</td>
										</tr>';
									}
									
								}
						$table_pdf.='			
								</table>';
							}
				$table_pdf.='			
					</td>
				</tr>
				';?>
				<?php
					}
$table_pdf.='</table>';
				}
				 
			}	
		}		
		?>
		<?php $table_pdf.='
	</page>
	
';
//echo $table_pdf;die;

if($getStubInfoNumRow>0) {
	$fp = fopen('new_html2pdf/pdffile.html','w+');
	//$filePut = fputs(fopen('testPdf.html','w+'),$table_print);
	$intBytes = fputs($fp,$table_pdf);
	//fclose($fileOpen);
	fclose($fp);
	?>
	
	  <form name="printlocal_anes" action="new_html2pdf/createPdf.php?op=p" method="post">
	 </form> 
	<!--  <form name="printlocal_anes" action="html2pdf/index.php?AddPage=p" method="post">
	 </form>  -->
	
	<script language="javascript">
		window.focus();
		function submitfn()
		{
			document.printlocal_anes.submit();
		}
	</script>
	<script type="text/javascript">
		submitfn();
	</script>
<?php 
}else { ?>
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr valign="top" height="20" bgcolor="#F8F9F7" class="text_10b"  style="font-size:11px; ">
			<td  align="center">No Record Found</td>
		</tr>	
	</table>
<?php
	}
?>
