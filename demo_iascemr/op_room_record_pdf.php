<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
//$tablename = "dischargesummarysheet";
//include("common/linkfile.php");
include("common_functions.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
include_once("new_header_print.php");
$pconfId = $_REQUEST['pConfId'];
if(!$pconfId) {
	$pconfId = $_SESSION['pConfId'];
}	
$patient_id = $_REQUEST["patient_id"];

$OpRoom_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '".$pconfId."'";
$OpRoom_patientConfirm_tblRes = imw_query($OpRoom_patientConfirm_tblQry) or die(imw_error());
$OpRoom_patientConfirm_tblRow = imw_fetch_array($OpRoom_patientConfirm_tblRes);
if(!$patient_id) {
	$patient_id = $OpRoom_patientConfirm_tblRow["patientId"];
}	

if(!$patient_id) {
	$patient_id = $_SESSION['patient_id'];
}	

$allrgiesQry=("select allergy_name,reaction_name from patient_allergies_tbl where patient_confirmation_id=$pconfId");
$allergiesRes = imw_query($allrgiesQry);
$allergiesnum=@imw_num_rows($allergiesRes);
$i=0;
/*while($detailsAllergy =@imw_fetch_array($allergiesRes))
{
   foreach($detailsAllergy as $key =>$val){
   		$detailsallergies[$i][$key] = $val;
   }
   $i++;
}*/
//print_r($detailsallergies);
$medsQry=("select prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_prescription_medication_tbl where confirmation_id=$pconfId ORDER BY prescription_medication_name");
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
																//CODE TO REMOVE ANES 1 SIGNATURE
																	if($_SESSION["loginUserId"]==$signAnesthesia1Id) {
																		$callJavaFunAnesDel = "document.frm_op_room.hiddSignatureId.value='TDanesthesia1NameId'; return displaySignature('TDanesthesia1NameId','TDanesthesia1SignatureId','op_room_record_ajaxSign.php','$loginUserId','Anesthesia1','delSign');";
																	}else {
																		$callJavaFunAnesDel = "alert('Only Dr. $Anesthesia1Name can remove this signature');";
																	}
																//END CODE TO REMOVE ANES 1 SIGNATURE

//


 $oproomQry="select *, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat, date_format(signNurse1DateTime,'%m-%d-%Y %h:%i %p') as signNurse1DateTimeFormat from operatingroomrecords where confirmation_id='".$pconfId."'";
$oproomRec=imw_query($oproomQry);
$ViewOpRoomRecordRow=imw_fetch_array($oproomRec);
$oproomNum =count($ViewOpRoomRecordRow);
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
		$iol_comments = stripslashes($ViewOpRoomRecordRow["iol_comments"]);
		$post2DischargeSummary = $ViewOpRoomRecordRow["post2DischargeSummary"];
		$post2OperativeReport = $ViewOpRoomRecordRow["post2OperativeReport"];
		//$model =explode(",", $ViewOpRoomRecordRow["model"]);
		$model =$ViewOpRoomRecordRow["model"];
		$Diopter =$ViewOpRoomRecordRow["Diopter"];
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
		
		$surgeryORNumber		= $ViewOpRoomRecordRow["surgeryORNumber"];
		$surgeryTimeIn 			= $objManageData->getTmFormat($ViewOpRoomRecordRow["surgeryTimeIn"]);
		$surgeryStartTime 		= $objManageData->getTmFormat($ViewOpRoomRecordRow["surgeryStartTime"]);
		$surgeryEndTime 		= $objManageData->getTmFormat($ViewOpRoomRecordRow["surgeryEndTime"]);
		$surgeryTimeOut 		= $objManageData->getTmFormat($ViewOpRoomRecordRow["surgeryTimeOut"]);
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
		
			list($EndHours,$EndMinutes) = explode(":",$surgeryEndTime);
			if($EndHours>=12){
			    $am_pm="PM";
			}
			else{
			  $am_pm="AM";
			}
			if($EndHours>=13){
			  $EndHours = $EndHours-12;
			   if(strlen($EndHours)==1){
			      $EndHours="0".$EndHours;
			   }
			}else
			{
			 //DO nothing
			}
			$surgeryEndTime = $EndHours.":".$EndMinutes." ".$am_pm;
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
		$iol_serial_number 	= $ViewOpRoomRecordRow["iol_serial_number"];		
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
		}else if($OpRoom_patientConfirmSiteTemp == 4) {
			$OpRoom_patientConfirmSite = "Left Upper Lid";
		}else if($OpRoom_patientConfirmSiteTemp == 5) {
			$OpRoom_patientConfirmSite = "Left Lower Lid";
		}else if($OpRoom_patientConfirmSiteTemp == 6) {
			$OpRoom_patientConfirmSite = "Right Upper Lid";
		}else if($OpRoom_patientConfirmSiteTemp == 7) {
			$OpRoom_patientConfirmSite = "Right Lower Lid";
		}else if($OpRoom_patientConfirmSiteTemp == 8) {
			$OpRoom_patientConfirmSite = "Bilateral Upper Lid";
		}else if($OpRoom_patientConfirmSiteTemp == 9) {
			$OpRoom_patientConfirmSite = "Bilateral Lower Lid";
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
	$tablePdfPrint.=$head_table;
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
						if($allergiesnum>0){
							while($detailsAllergy=@imw_fetch_array($allergiesRes)){
								$tablePdfPrint.='
								<tr>
									<td style="width:200px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($detailsAllergy['allergy_name'])).'</td>
									<td style="width:145px; border-right:1px solid #C0C0C0;" class="bdrbtm pl5">'.stripslashes(htmlentities($detailsAllergy['reaction_name'])).'</td>
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
							$tablePdfPrint.="<br>Vancomycin 0.1 ml (10mg)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
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
								/*
								if($post2DischargeSummary){
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
									$condArray['confirmation_id']	=	$pconfId ;
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
								if($OtherSuppliesUsed)
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
										<td style="width:50px;" class="bold bdrbtm">Model:</td>
										<td style="width:100px;" class="bdrbtm">';
										if(trim($model)){
											$tablePdfPrint.=$model;
										}else{$tablePdfPrint.="____";}
										$tablePdfPrint.='
										</td>
										<td style="width:90px;vertical-align:top;" class="bold bdrbtm">Diopter:</td>
										<td style="width:110px;vertical-align:top;" class="bdrbtm">';
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
				<td style="width:385px;">
					<table style="width:385px;border-right:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:100px;" class="bdrbtm">Solumedrol:&nbsp;</td>
							<td style="width:15px;" class="cbold bdrbtm">';
							if($Solumedrol){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:100px;" class="bdrbtm">';
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
							<td style="width:80px;" class="bdrbtm">';
							if($AncefList){
								$tablePdfPrint.=$AncefList;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>
							<td style="width:100px;" class="bdrbtm">Dexamethasone:</td>
							<td style="width:15px;" class="cbold bdrbtm">';
							if($Dexamethasone){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:100px;" class="bdrbtm">';
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
							<td style="width:80px;" class="bdrbtm">';
							if($GentamicinList){
								$tablePdfPrint.=$GentamicinList;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>
							<td style="width:100px;" class="bdrbtm">Kenalog:</td>
							<td style="width:15px;" class="cbold bdrbtm">';
							if($Kenalog){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:100px;" class="bdrbtm">';
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
							<td style="width:80px;" class="bdrbtm">';
							if($DepomedrolList){
								$tablePdfPrint.=$DepomedrolList;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>
							<td style="width:100px;" class="bdrbtm">Vancomycin:</td>
							<td style="width:15px;" class="cbold bdrbtm">';
							if($Vancomycin){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:100px;" class="bdrbtm">';
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
							<td style="width:80px;" class="bdrbtm">';
							if($TrimaxiList){
								$tablePdfPrint.=$TrimaxiList;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>	
							<td style="width:100px;" class="bdrbtm">XylocaineMPF:&nbsp;</td>
							<td style="width:15px;" class="bdrbtm cbold">';
								$tablePdfPrint.=($injXylocaineMPF ? "Y" : "___").
							'</td>
							<td style="width:100px;" class="bdrbtm">';
								$tablePdfPrint.=($injXylocaineMPFList ? $injXylocaineMPFList : "___").
							'</td>
							<td style="width:75px;" class="bdrbtm">Miostat:&nbsp;</td>
							<td style="width:15px;" class="bdrbtm cbold">';
								$tablePdfPrint.=($injMiostat ? "Y" : "___").
							'</td>
							<td style="width:80px;" class="bdrbtm">';
								$tablePdfPrint.=($injMiostatList ? $injMiostatList : "___").
							'</td>
						</tr>
						<tr>
							<td style="width:100px;" class="bdrbtm">Phenyl/Lido 1.5%/1%:</td>
							<td style="width:15px;" class="cbold bdrbtm">';
							if($PhenylLido){
								$tablePdfPrint.="Y";
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td style="width:100px;" class="bdrbtm">';
							if($PhenylLidoList){
								$tablePdfPrint.=$PhenylLidoList;	
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='
							</td>
							<td  colspan="3" style="width:170px;" class="bdrbtm">Other:&nbsp;';
							if($postOpInjOther){
								$tablePdfPrint.=$postOpInjOther;
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.=
							'</td>
						</tr>
						<tr>
							<td colspan="6" style="width:385px;" class="bdrbtm">Patch:&nbsp;';
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
							<td colspan="5" style="width:285px;" class="bdrbtm">';
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
							<td colspan="5" style="width:285px;" class="bdrbtm">';
							if(stripslashes($intraOpPostOpOrders)){
								$tablePdfPrint.=stripslashes($intraOpPostOpOrders);	
							}else{$tablePdfPrint.="____";}
							$tablePdfPrint.='
							</td>
						</tr>
						<tr>
							<td style="width:100px;" class="bdrbtm bold">Nurse Notes:</td>
							<td colspan="5" style="width:285px;" class="bdrbtm">';
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
							<td colspan="5" style="width:285px;" class="bdrbtm">';
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
				<td style="width:315px;vertical-align:top;">
					<table style="width:315px;" cellpadding="0" cellspacing="0">
						<tr>
							<td style="width:315px;">Anesthesia service provided:&nbsp;';
							if(trim($anesthesia_service)=="full_anesthesia"){
								$tablePdfPrint.="<b>Full</b>";	
							}else if(trim($anesthesia_service)=="no_anesthesia"){
								$tablePdfPrint.="<b>No</b>";		
							}else{$tablePdfPrint.="___";}
							$tablePdfPrint.='</td>
						</tr>
						<tr>
							<td style="width:315px;" class="bdrbtm pl5">';
							if(trim($TopicalBlock)!=""){
								$tablePdfPrint.=$TopicalBlock.":&nbsp;<b>Y</b>";	
							}else{$tablePdfPrint.="Block:&nbsp;___&nbsp;&nbsp;Local:&nbsp;___&nbsp;&nbsp;Topical:___";}
							
							$tablePdfPrint.='
							</td>
						</tr>
						<tr style="width:315px;" class="bdrbtm pl5">	
							<td>
								 <table style="width:315px;" cellpadding="0" cellspacing="0">';
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
										<td colspan="'.$tmp_colspan.'" style="width:315px;">
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
					$condArr['confirmation_id']	=	$pconfId ;
					$condArr['chartName']				=	'intra_op_record_form' ;
					
					$gridData		=	$objManageData->getMultiChkArrayRecords('vital_sign_grid',$condArr,'start_time','Asc');
		
					$gCounter	=	0;
					if(is_array($gridData) && count($gridData) > 0  )
					{
						foreach($gridData as $gridRow)
						{		
							$gCounter++;
							//$fieldValue1= date('h:i A', strtotime($gridRow->start_time));
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
								//$tablePdfPrint.="<br><b>Signature Date:&nbsp;</b>".$OpRoom_patientConfirmDos.' '.$verifiedbyNurseTime;
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
					echo '<page></page>';												
				}
			}
			
			$tablePdfPrint.='
			<br><table style="width:744px; text-align:center; border:1px solid #C0C0C0; " cellpadding="0" cellspacing="0">
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
					$newSize = $objManageData->imageResize(680,400,500);						
					$priImageSize[0] = 500;
				}					
				elseif($priImageSize[1] > 840){
					$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],600);						
					$priImageSize[1] =600;
				}
				else{					
					$newSize = $priImageSize[3];
				}							
				if($priImageSize[1] > 800 ){					
					echo '<page></page>';														
				}
			}
			$tablePdfPrint.='
			<br><table style="width:744px; text-align:center; border:1px solid #C0C0C0; " cellpadding="0" cellspacing="0">
				
				<tr>
					<td style="width:744px; text-align:center;" class="cbold" ><img src="../html2pdfnew/oproom1.jpg" '.$newSize.'></td>
				</tr>
			</table>';
		}
	 	
		//die($tablePdfPrint);
$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$tablePdfPrint);
fclose($fileOpen);
$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';

if($opRoomFormStatus=='completed' || $opRoomFormStatus=='not completed') {
?>	

 <form name="printOproom" action="new_html2pdf/createPdf.php?op=p" method="post">
 </form>

<script language="javascript">
	function submitfn()
	{
		document.printOproom.submit();
	}
</script>

<script type="text/javascript">
	submitfn();
</script>
<?php
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>	

