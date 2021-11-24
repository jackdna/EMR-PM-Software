<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
session_start();
$loginUser = $_SESSION['iolink_loginUserId'];
$loginUserName = $_SESSION['iolink_loginUserName'];
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
include_once("common/functions.php");
include_once("common/link_new_file.php");
include("common/iOlinkFunction.php");
include("common/iOLinkCommonFunction.php");

$practiceName = getPracticeName($loginUser,'Coordinator');
$coordinatorType = getCoordinatorType($loginUser);
//START GET SURGERYCENTER NAME
$getSurgeryCenterDetail = $objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', 1);
if($getSurgeryCenterDetail) {
	$surgerycenterName = $getSurgeryCenterDetail->name;
	$show_religion = $getSurgeryCenterDetail->show_religion;
}	
//END GET SURGERYCENTER NAME

//START GET FACILITY-ID
if(trim($_SESSION['iolink_iasc_facility_id'])) {
	$iasc_facility_id = $_SESSION['iolink_iasc_facility_id'];
}else if(trim($constantImwFacilityId)) {
	$iasc_facility_id = $constantImwFacilityId;
}
if(!$iasc_facility_id) {
	$iasc_facility_id = '1';
}
//END GET FACILITY-ID

$patient_in_waiting_id = isset($_REQUEST['patient_in_waiting_id']) ? $_REQUEST['patient_in_waiting_id'] : 0;
$patient_id 	= isset($_REQUEST['patient_id']) ? $_REQUEST['patient_id'] : 0;
$pt_wait_book 	= isset($_REQUEST['pt_wait_book']) ? $_REQUEST['pt_wait_book'] : ''; 
$pt_wait_move 	= isset($_REQUEST['pt_wait_move']) ? $_REQUEST['pt_wait_move'] : ''; 
$mode 			= isset($_REQUEST["mode"] ) ? $_REQUEST["mode"] : '' ;
$patientStatus	= isset($_REQUEST['pat_status']) ? $_REQUEST['pat_status'] : '';
$pt_wait_submit = isset($_POST["pt_wait_submit"]) ? $_POST["pt_wait_submit"] : '';



//search_patient	
if($pt_wait_submit<>"") {
	
	$inCorrectZipCode = $_REQUEST['inCorrectZipCode'];
	//START GET SURGEON NAME 
	$surgeon_name_id = $_POST["surgeon_name_id"];
	if($surgeon_name_id) {
		$postSurgeosDetails = $objManageData->getRowRecord('users', 'usersId', $surgeon_name_id);
		$postSurgeonFname='';
		$postSurgeonMname='';
		$postSurgeonLname='';
		if($postSurgeosDetails){
			$iolinkMaxBooking = $postSurgeosDetails->iolink_max_booking;
			$postUsersId = $postSurgeosDetails->usersId;
			$postSurgeonFname = trim($postSurgeosDetails->fname);
			$postSurgeonMname = trim($postSurgeosDetails->mname);
			$postSurgeonLname = trim($postSurgeosDetails->lname);
			$postSurgeonNpi   = trim($postSurgeosDetails->npi);
			
			$postSurgeonName = $postSurgeonLname.', '.$postSurgeonFname.' '.$postSurgeonMname;
		}	
	}	
	//END GET SURGEON NAME
	//PATIENT DOS
	$patient_dos = $_POST["dos"];
	$patient_dos_temp='';
	if($patient_dos) {
		$patient_dos_split = explode("-",$patient_dos);
		$patient_dos_temp = $patient_dos_split[2]."-".$patient_dos_split[0]."-".$patient_dos_split[1];
	}
	//PATIENT DOS
	
	//START CODE TO CHECK IF SURGEON IS AVAILABLE ON SPECIFIED DATE OF SURGERY
	$boolBookPt = '';
	$iAscSurgeonId = getiAscUserId($postSurgeonFname,$postSurgeonMname,$postSurgeonLname,$postSurgeonNpi);
	if($iAscSurgeonId) {
		$surgeonAvail = month_scheduleprovider_Highlight($patient_dos_temp,'',$iAscSurgeonId,$iasc_facility_id);
		if($surgeonAvail=='yes' || constant("STOP_CHECK_IMW_SCHEDULE")=="YES") {
			$boolBookPt = 'yes';
		}
	}
	//END CODE TO CHECK IF SURGEON IS AVAILABLE ON SPECIFIED DATE OF SURGERY
	
	
	//START CODE TO SET MAXIMUM RECORD LIMIT PER DAY FOR SURGEON IN patient_in_waiting TABLE
	$maxBookSurgeon='true';
	$chkAndWaitingIdQry='';
	if($patient_in_waiting_id) { 
		$chkAndWaitingIdQry = "AND patient_in_waiting_id!='".$patient_in_waiting_id."'";
	}
	$chkNumberOfPatientInWaitingQry = "SELECT patient_in_waiting_id FROM patient_in_waiting_tbl 
										WHERE patient_status!='Canceled'
										AND dos='".$patient_dos_temp."'
										AND surgeon_fname='".addslashes($postSurgeonFname)."'
										AND surgeon_mname='".addslashes($postSurgeonMname)."'
										AND surgeon_lname='".addslashes($postSurgeonLname)."'
										$chkAndWaitingIdQry
										";
	
	$chkNumberOfPatientInWaitingRes = imw_query($chkNumberOfPatientInWaitingQry) or die(imw_error());
	$chkNumberOfPatientInWaitingNumRow = imw_num_rows($chkNumberOfPatientInWaitingRes);
	if($chkNumberOfPatientInWaitingNumRow>=$iolinkMaxBooking) {
		$maxBookSurgeon='false';
	}
	//END CODE TO SET MAXIMUM RECORD LIMIT PER DAY FOR SURGEON IN patient_in_waiting TABLE
	
	//START CHECK IN CASE OF UPDATE
	if($patient_in_waiting_id) {
		$chkUpdatedPatientInWaitingQry = "SELECT patient_in_waiting_id FROM patient_in_waiting_tbl 
											WHERE patient_status!='Canceled'
											AND dos='".$patient_dos_temp."'
											AND patient_in_waiting_id='".$patient_in_waiting_id."'
											";
		
		$chkUpdatedPatientInWaitingRes = imw_query($chkUpdatedPatientInWaitingQry) or die(imw_error());
		$chkUpdatedPatientInWaitingNumRow = imw_num_rows($chkUpdatedPatientInWaitingRes);
		if($chkUpdatedPatientInWaitingNumRow>0) {
			$maxBookSurgeon='true';
		}
	
	}	
	//END CHECK IN CASE OF UPDATE
	
	if($maxBookSurgeon=='false') {
	?>
		<script>
			var surgerycenterName = '<?php echo $surgerycenterName;?>';
			var postSurgeonName = '<?php echo $postSurgeonName;?>';
			var patient_dos = '<?php echo $patient_dos;?>';
			alert('Please call '+surgerycenterName+' for additional booking on '+patient_dos+' for Dr. '+postSurgeonName);
		</script>
	<?php	
		echo "<script>waitingPatient_info(\"$patient_id\",\"$patient_in_waiting_id\",'',\"".$_REQUEST['pat_status']."\");</script>";
	}else {
	
		if($inCorrectZipCode=='true') {
			//START CODE TO ADD/EDIT ZIPCODE VALUE IN ZIP-CODE TABLE
			addEditZipCodeFun(addslashes($_POST["city"]),addslashes($_POST["state"]),addslashes($_POST["zip"])); 
			//END CODE TO ADD/EDIT ZIPCODE VALUE IN ZIP-CODE TABLE
		}
		
		$patient_dob = $_POST["dob"];
		
		if($patient_dob) {
			$patient_dob_split = explode("-",$patient_dob);
			$patient_dob_temp = $patient_dob_split[2]."-".$patient_dob_split[0]."-".$patient_dob_split[1];	
		}
		
		$address1_idPost = ucwords(trim($_POST["address1_id"]));
		$address1_idPost=str_ireplace(', ',',',$address1_idPost);
		$address1_idPost=str_ireplace(',',', ',$address1_idPost);
		
		$address2_idPost = ucwords(trim($_POST["address2_id"]));
		$address2_idPost=str_ireplace(', ',',',$address2_idPost);
		$address2_idPost=str_ireplace(',',', ',$address2_idPost);
		unset($arrayPatientDataRecord);
		$arrayPatientDataRecord['title'] = addslashes($_POST["title"]);
		$arrayPatientDataRecord['patient_fname'] = addslashes(ucwords(trim($_POST["first_name"])));
		$arrayPatientDataRecord['patient_mname'] = addslashes(ucwords(trim($_POST["middle_name"])));
		$arrayPatientDataRecord['patient_lname'] = addslashes(ucwords(trim($_POST["last_name"])));
		$arrayPatientDataRecord['patient_suffix'] = addslashes(ucwords(trim($_POST["patient_suffix"])));
		$arrayPatientDataRecord['street1'] = addslashes($address1_idPost);
		$arrayPatientDataRecord['street2'] = addslashes($address2_idPost);
		$arrayPatientDataRecord['city'] = addslashes(ucwords(trim($_POST["city"])));
		$arrayPatientDataRecord['state'] = addslashes(ucwords(trim($_POST["state"])));
		$arrayPatientDataRecord['zip'] = addslashes(trim($_POST["zip"]));
		$arrayPatientDataRecord['date_of_birth'] = $patient_dob_temp;
		$arrayPatientDataRecord['sex'] = $_POST["sex_list"];
		$arrayPatientDataRecord['homePhone'] = $_POST["home_phone"];
		$arrayPatientDataRecord['workPhone'] = $_POST["work_phone"];
		$arrayPatientDataRecord['religion'] = addslashes(ucwords(trim($_POST["religion"])));
		
		if(!$patient_id) {
			unset($conditionArr);
			$conditionArr['patient_fname'] = addslashes(ucwords(trim($_POST["first_name"])));
			$conditionArr['patient_lname'] = addslashes(ucwords(trim($_POST["last_name"])));
			$conditionArr['date_of_birth'] = $patient_dob_temp;
			$conditionArr['zip'] = $_POST["zip"];
			$chkPatientDataTblDetails = $objManageData->getMultiChkArrayRecords('patient_data_tbl', $conditionArr,'patient_id','ASC');
			if($chkPatientDataTblDetails) { //IN CASE TO ADD PATIENT INFO
				foreach($chkPatientDataTblDetails as $patientDataTblDetails){
					//$insertPatientDataId = $patientDataTblDetails->patient_id;
					$patient_id = $patientDataTblDetails->patient_id;
				}
			}else {
				$patient_id = $objManageData->addRecords($arrayPatientDataRecord, 'patient_data_tbl');
				$_SESSION['HL7_ADT_FLAG'] = true;
			}	
		}	
		if($patient_id) {
			/*
			$chkPatientExistQry = "SELECT * FROM patient_data_tbl 
									WHERE patient_fname='".addslashes($_POST["first_name"])."'
									  AND patient_lname='".addslashes($_POST["last_name"])."'
									  AND date_of_birth='".$patient_dob_temp."'
									  AND zip='".$_POST["zip"]."'
									  AND patient_id!='".$patient_id."'
									";

			$chkPatientExistRes = imw_query($chkPatientExistQry) or die(imw_error());
			$chkPatientExistNumRow = imw_num_rows($chkPatientExistRes);
			if($chkPatientExistNumRow) {//IN CASE TO UPDATE PATIENT INFO 
				echo "<script>alert('This patient has already been exist');</script>";
				$insertPatientDataId = $patient_id;
			
			//}else {
			*/
				if($boolBookPt!='yes') {
					echo "<script>
							alert('Surgeon Dr. ".$postSurgeonName." is not available in iAsc on ".$patient_dos."');
						  	waitingPatient_info(\"$patient_id\",\"$patient_in_waiting_id\",\"\",\"".$_REQUEST['pat_status']."\");
						  </script>";
					
				}else {
					$insertPatientDataId = $objManageData->updateRecords($arrayPatientDataRecord, 'patient_data_tbl', 'patient_id', $patient_id);
					
					if( !isset($_SESSION['HL7_ADT_FLAG']) || $_SESSION['HL7_ADT_FLAG'] !== true )
						$_SESSION['HL7_ADT_FLAG'] = 'update';
				
					//SAVE INFO IN patient_waiting_tbl
					$surgery_time_temp = trim($_POST["surgery_time"]);
					$surgery_time='';
					if($surgery_time_temp) {
						//surgery_time saved in database
						   $surgery_time_split = explode(" ",$surgery_time_temp);
							if($surgery_time_split[1]=="PM" || $surgery_time_split[1]=="pm") {
								$time_further_split = explode(":",$surgery_time_split[0]);
								if($time_further_split[0]!=12) { 	
									$surgery_timeIncr=$time_further_split[0]+12; 
								}else { $surgery_timeIncr=$time_further_split[0];}
								$surgery_time = $surgery_timeIncr.":".$time_further_split[1].":00";
							
							}elseif($surgery_time_split[1]=="AM" || $surgery_time_split[1]=="am") {
								$time_further_split = explode(":",$surgery_time_split[0]);
								if($time_further_split[0]==12) { $time_further_split[0]='00'; }
								$surgery_time=$time_further_split[0].":".$time_further_split[1].":00";
							}
					}
					
					
					
					unset($arrayPatientInWaitingRecord);
					//START SURGERY DETAIL
					$arrayPatientInWaitingRecord['surgery_time'] 		= $surgery_time;
					$arrayPatientInWaitingRecord['surgeon_fname'] 		= addslashes($postSurgeonFname);
					$arrayPatientInWaitingRecord['surgeon_mname'] 		= addslashes($postSurgeonMname);
					$arrayPatientInWaitingRecord['surgeon_lname'] 		= addslashes($postSurgeonLname);
					$arrayPatientInWaitingRecord['patient_primary_procedure']= addslashes($_POST["prim_proc"]);
					$arrayPatientInWaitingRecord['patient_secondary_procedure']= addslashes($_POST["sec_proc"]);
					$arrayPatientInWaitingRecord['patient_tertiary_procedure']= addslashes($_POST["ter_proc"]);
					
					$arrayPatientInWaitingRecord['primaryPolicyNumber'] = $_POST["primaryPolicyNumber"];
					$arrayPatientInWaitingRecord['primaryGroupNumber'] 	= $_POST["primaryGroupNumber"];
					$arrayPatientInWaitingRecord['primaryPlaneName'] 	= $_POST["primaryPlaneName"];
					
					$arrayPatientInWaitingRecord['secondaryPolicyNumber']= $_POST["secondaryPolicyNumber"];
					$arrayPatientInWaitingRecord['secondaryGroupNumber']= $_POST["secondaryGroupNumber"];
					$arrayPatientInWaitingRecord['secondaryPlaneName'] 	= $_POST["secondaryPlaneName"];
					
					$arrayPatientInWaitingRecord['arrival_time'] 		= $_POST["arrival_time"];
					$arrayPatientInWaitingRecord['pickup_time'] 		= $_POST["pickup_time"];
					
					$arrayPatientInWaitingRecord['iasc_facility_id'] 	= $iasc_facility_id;
					$arrayPatientInWaitingRecord['dos'] 				= $patient_dos_temp;
					$arrayPatientInWaitingRecord['site'] 				= $_POST["site"];
					
					$arrayPatientInWaitingRecord['patient_status'] 		= "Scheduled";
					$arrayPatientInWaitingRecord['patient_id'] 			= $patient_id;
					$arrayPatientInWaitingRecord['transportReq'] 		= $_POST["transportReq"];
					
					$transportReqCmnt 		= "";
					$transportCmntSpace		= "";
					$chkTransportReqPostComment = stristr($_POST["comment"],"Transportation Requested");
					if($_POST["transportReq"]=="1") {
						if(trim($_POST["comment"])) { $transportCmntSpace = " "; }
						if(!$chkTransportReqPostComment) {$transportReqCmnt = $transportCmntSpace."Transportation Requested";}	
					}else {
						$_POST["comment"] =str_ireplace("Transportation Requested","",$_POST["comment"]);	
					}
					$arrayPatientInWaitingRecord['comment'] 			= addslashes($_POST["comment"].$transportReqCmnt);
					$arrayPatientInWaitingRecord['operator_id'] 		= $loginUser;
					
					//END SURGERY DETAIL
					
					$patientSiteImw	='';
					if($_POST["site"]) {
						$patientSiteImw=$_POST["site"];
						if(trim($_POST["site"])=='both') {
							$patientSiteImw='bilateral';
						}
						$patientSiteImw = ucfirst($patientSiteImw);
					}
					if($pt_wait_book || $pt_wait_move) { 
						unset($conditionArr);
						$conditionArr['patient_id'] 	= $patient_id;
						$conditionArr['dos'] 			= $patient_dos_temp;
						$conditionArr['patient_status'] = 'Scheduled';
						$chkPatientWaitingDetails = $objManageData->getMultiChkArrayRecords('patient_in_waiting_tbl', $conditionArr);
					}
					if($patient_in_waiting_id) { //IN CASE TO SAVE THE RECORD
						$chkPatientAlreadyScheduledQry = "SELECT patient_in_waiting_id FROM patient_in_waiting_tbl 
															WHERE patient_id='".$patient_id."'
															AND dos='".$patient_dos_temp."'
															AND patient_status!='Canceled'
															AND patient_in_waiting_id!='".$patient_in_waiting_id."'";
						$chkPatientAlreadyScheduledRes = imw_query($chkPatientAlreadyScheduledQry) or die(imw_error());
						$chkPatientAlreadyScheduledNumRow = imw_num_rows($chkPatientAlreadyScheduledRes);
						
					}
					//START CODE TO RESET COMMENT STATUS IN SCEMR TO OVERWRITE EXISTING COMMENT FROM iASCLink
					$resetScemrCommentQry = "";
					if($_REQUEST["scemr_comment_status_to_reset"] == "yes" && $patient_in_waiting_id) {
						$resetScemrCommentQry = "UPDATE stub_tbl SET comment_modified_status = '0', comment_status_reset_datetime = '".date("Y-m-d H:i:s")."', comment_status_reset_by_operator = '".$_SESSION["iolink_loginUserId"]."' WHERE iolink_patient_in_waiting_id = '".$patient_in_waiting_id."' AND iolink_patient_in_waiting_id != '0' ";	
					}
					//END CODE TO RESET COMMENT STATUS IN SCEMR TO OVERWRITE EXISTING COMMENT FROM iASCLink
					
					if($pt_wait_book) { 	
						/*if($chkPatientWaitingDetails) {
							//DO NOT INSERT PATIENT SCHEDULE AGAIN
							echo "<script>alert('This patient has already been scheduled for this day');</script>";
						}else {*/
							//ADD RECORD
							$patient_in_waiting_id = $objManageData->addRecords($arrayPatientInWaitingRecord, 'patient_in_waiting_tbl');	
							
							//START SET PRIMARY AND SECONDARY INSURANCE 
							unset($arrayPriSecInsRecord);
							$arrayPriSecInsRecord['patient_id'] = $patient_id;
							$arrayPriSecInsRecord['waiting_id'] = $patient_in_waiting_id;
							$objManageData->updateRecords($arrayPriSecInsRecord, 'insurance_data', 'waiting_id', '');
							//END SET PRIMARY AND SECONDARY INSURANCE 
							echo "<script>alert('Record Booked');</script>";
							$_REQUEST['pat_status']="";
						//}
						$_SESSION['HL7_SIU_STATUS_CODE'] = '0';	/*HL7 Status Code*/
					}else if($patient_in_waiting_id && $pt_wait_move) {
						/*
						if($chkPatientAlreadyScheduledNumRow>0) {
							//DO NOT INSERT PATIENT SCHEDULE AGAIN
							echo "<script>alert('This patient has already been scheduled for this day');</script>";
						}else {*/
							//UPDATE RECORD
							$alert_msg='Record Moved';
							if($_REQUEST['pat_booking_id']!="" || $_REQUEST['pat_status']=="Canceled"){
								$alert_msg='Record Booked';
							}							
							$objManageData->updateRecords($arrayPatientInWaitingRecord, 'patient_in_waiting_tbl', 'patient_in_waiting_id', $patient_in_waiting_id);
							//START CODE TO RESET COMMENT STATUS IN SCEMR TO OVERWRITE EXISTING COMMENT FROM iASCLink
							if($resetScemrCommentQry) {
								$resetScemrCommentRes = imw_query($resetScemrCommentQry) or die($resetScemrCommentQry.imw_error());
							}
							//END CODE TO RESET COMMENT STATUS IN SCEMR TO OVERWRITE EXISTING COMMENT FROM iASCLink
							
							setReSyncroStatus($patient_in_waiting_id,'ptDetail');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
							
							//START CODE TO MAKE SAME EFFECT IN iASC
							$blUpdateNew = false;$imedicSurgeryEndTime='';
							logApptChangedStatus($patient_in_waiting_id, $patient_dos_temp, $surgery_time, $imedicSurgeryEndTime, '202', '', '', 'iolink', $_POST["comment"], '',$blUpdateNew);
							updateScheduleApptDetails($patient_in_waiting_id, $patient_dos_temp, $surgery_time, $imedicSurgeryEndTime, '202', '', '', 'iolink', $_POST["comment"], '',$blUpdateNew,$_POST["pickup_time"],$_POST["pickup_time"],$patientSiteImw);
							//END CODE TO MAKE SAME EFFECT IN iASC
							echo "<script>alert('".$alert_msg."');</script>";
							$_REQUEST['pat_status']="";
						//}
						$_SESSION['HL7_SIU_STATUS_CODE'] = '202';	/*HL7 Status Code*/
					}else if(!$pt_wait_book && !$pt_wait_move) {
						if($patient_in_waiting_id) {
							/*
							if($chkPatientAlreadyScheduledNumRow>0) {
								//DO NOT INSERT PATIENT SCHEDULE AGAIN
								echo "<script>alert('This patient has already been scheduled for this day');</script>";
							}else {	*/
								//UPDATE RECORD
								$arrayPatientInWaitingRecord['iAscReSyncroStatus'] 		= 'yes';
								$arrayPatientInWaitingRecord['reSyncroVia'] 			= 'ptDetail';
								$objManageData->updateRecords($arrayPatientInWaitingRecord, 'patient_in_waiting_tbl', 'patient_in_waiting_id', $patient_in_waiting_id);

								//START CODE TO RESET COMMENT STATUS IN SCEMR TO OVERWRITE EXISTING COMMENT FROM iASCLink
								if($resetScemrCommentQry) {
									$resetScemrCommentRes = imw_query($resetScemrCommentQry) or die($resetScemrCommentQry.imw_error());
								}
								//END CODE TO RESET COMMENT STATUS IN SCEMR TO OVERWRITE EXISTING COMMENT FROM iASCLink
								
								setReSyncroStatus($patient_in_waiting_id,'ptDetail');//CALL FUNCTION TO SET Re-Syncro status(CHANGE BACKGROUNG COLOR TO ORANGE)
								echo "<script>alert('Record Updated');</script>";
								$_REQUEST['pat_status']="";
							//}
							$_SESSION['HL7_SIU_STATUS_CODE'] = '6666';	/*HL7 Status Code S14(Update) - SCH.25 Blank*/
						}else {
							//ADD RECORD
							$patient_in_waiting_id = $objManageData->addRecords($arrayPatientInWaitingRecord, 'patient_in_waiting_tbl');	
							
							//START SET PRIMARY AND SECONDARY INSURANCE 
							unset($arrayPriSecInsRecord);
							$arrayPriSecInsRecord['patient_id'] = $patient_id;
							$arrayPriSecInsRecord['waiting_id'] = $patient_in_waiting_id;
							$objManageData->updateRecords($arrayPriSecInsRecord, 'insurance_data', 'waiting_id', '');
							//END SET PRIMARY AND SECONDARY INSURANCE 
							echo "<script>alert('Record Saved');</script>";
							$_REQUEST['pat_status']="";
							
							$_SESSION['HL7_SIU_STATUS_CODE'] = '0';	/*Send HL7 S14 (Modification) - SCH-25 blank*/
						}
					}
					
					/*Add New Patient in iDoc.*/
					if(isset($_SESSION['HL7_ADT_FLAG']) && (defined('HL7_ADT_UPDATE') || defined('HL7_SIU_GENERATION'))){
						$adtMessageType = ($_SESSION['HL7_ADT_FLAG']==='update') ? 'Update_Patient' : 'Add_New_Patient';
						
						unset($_SESSION['HL7_ADT_FLAG']);

						$cur = curl_init();
						$url = 'http://localhost/'.$iolinkDirectoryName.'/sync_demographics.php?sync=yes&selDos='.$patient_dos_temp.'&multiPatientInWaitingId='.$patient_in_waiting_id;
						curl_setopt($cur,CURLOPT_URL,$url);
						curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
						curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
						$data = curl_exec($cur);

						$idocinserterror = '';
						if (curl_errno($cur)){
							$idocinserterror =  "Curl Error iOLink to iDOC: " . curl_error($cur);
						}
						curl_close($cur);

						/*Make HL7 Messages*/
						$sql_imw_id = 'SELECT `imwPatientId` FROM `patient_data_tbl` WHERE `patient_id`='.$patient_id;
						$imw_id_resp = imw_query($sql_imw_id);
						if($imw_id_resp && imw_num_rows($imw_id_resp)==1){
							$imw_pt_id = imw_fetch_assoc($imw_id_resp);
							$imw_pt_id = (int)$imw_pt_id['imwPatientId'];	/*IMW Patient Id*/
							if($imw_pt_id>0){
								$patient_in_waiting_id_bk = $patient_in_waiting_id;	/*Backup waiting ID*/
								$ignoreAuth = true;
								
								/*Make HL7 ADT message for the added patient*/
								if( 
									(is_hl7_generate('adt') && $adtMessageType === 'Add_New_Patient')
									||
									(
										defined( 'HL7_ADT_UPDATE') && HL7_ADT_UPDATE === true &&
										$adtMessageType === 'Update_Patient'
									)
								)
								{
									/*Include file and set configuration variables as per iDoc Version - as R8 have some specific requirements*/
									$URIbk = $_SERVER['REQUEST_URI'];
									if( 
										isset($imwVer) && $imwVer === 'R8' && 
										isset($imwPracName) && !empty($imwPracName)
									)
									{
										$_SERVER['REQUEST_URI'] = $imwPracName;
										$_SERVER['HTTP_HOST']	= $imwPracName;
										$curlFields = array();
										$curlFields['MsgType'] 			= 'ADT';
										$curlFields['PatId'] 			= $imw_pt_id;
										$curlFields['SubMsgType'] 		= $adtMessageType;
										$url = $imwPracticeURL.'/hl7sys/api/index.php';
										$cur = curl_init();
										curl_setopt($cur,CURLOPT_URL,$url);
										curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
										curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
										curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
										curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
										curl_setopt($cur, CURLOPT_POSTFIELDS, $curlFields);
										$data = curl_exec($cur);
										if (curl_errno($cur)){
										//	die("Curl Error (HL7): " . curl_error($cur));
										}
										curl_close($cur);
									}
									else{
										require_once($_SERVER['DOCUMENT_ROOT'].'/'.$imwDirectoryName."/interface/patient_info/CLS_makeHL7.php");
										$makeHL7 = new makeHL7;
										$makeHL7->log_HL7_message($imw_pt_id, $adtMessageType);
									}
									$_SERVER['REQUEST_URI'] = $URIbk;
									unset($URIbk, $makeHL7);
									
									include("common/conDb.php");
								}
								/*End HL7 ADT message for the added patient*/
								
								/*Make HL7 SIU message for the added Patient*/
								if( 
									defined('HL7_SIU_GENERATION') && constant('HL7_SIU_GENERATION') === true &&
									isset($imwVer) && $imwVer === 'R8' && 
									isset($imwPracName) && !empty($imwPracName) &&
									array_key_exists('HL7_SIU_STATUS_CODE', $_SESSION) && 
									$_SESSION['HL7_SIU_STATUS_CODE'] != ''
								)
								{
									imw_close($link);
									include('connect_imwemr.php');
									
									$sqlIdocApptId = 'SELECT `id` FROM `schedule_appointments` WHERE `iolink_iosync_waiting_id` = '.$patient_in_waiting_id_bk;
									
									$respIdocApptId = imw_query($sqlIdocApptId);
									if( $respIdocApptId && imw_num_rows($respIdocApptId) > 0 )
									{
										$idocApptId = imw_fetch_assoc($respIdocApptId);
										$idocApptId = $idocApptId['id'];
										
										$URIbk = $_SERVER['REQUEST_URI'];
										$_SERVER['REQUEST_URI'] = $imwPracName;
										$_SERVER['HTTP_HOST']	= $imwPracName;										
										$curlFields = array();
										$curlFields['MsgType'] 			= 'SIU';
										$curlFields['PatId'] 			= $imw_pt_id;
										$curlFields['SchId'] 			= $idocApptId;
										$curlFields['SubMsgType'] 		= $_SESSION['HL7_SIU_STATUS_CODE'];
										unset($_SESSION['HL7_SIU_STATUS_CODE']);
										$url = $imwPracticeURL.'/hl7sys/api/index.php';
										$cur = curl_init();
										curl_setopt($cur,CURLOPT_URL,$url);
										curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
										curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
										curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
										curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
										curl_setopt($cur, CURLOPT_POSTFIELDS, $curlFields);
										$data = curl_exec($cur);
										if (curl_errno($cur)){
										//	die("Curl Error (HL7): " . curl_error($cur));
										}
										curl_close($cur);
										
										$_SERVER['REQUEST_URI'] = $URIbk;
										unset($URIbk, $makeHL7);
									}
									include('common/conDb.php');
								}
								/*END HL7 SIU message for the added Patient*/
								$patient_in_waiting_id = $patient_in_waiting_id_bk;	/*Backup waiting ID*/
								unset($patient_in_waiting_id_bk);
							}
						}
						/*End HL7 Messages*/
						
						if( 
							isset($imwVer) && $imwVer === 'R8' && 
							isset($imwPracName) && !empty($imwPracName)
							&& function_exists('imw_close')
						){
							imw_close();	/*Close imwemr databse connection*/
							$GLOBALS['dbh'] = false;
						}
						
					}
					/*Add New Patient in iDoc.*/
					
					//RELOAD BOOKING SHEET
					echo "<script>
							waitingPatient_info(\"$patient_id\",\"$patient_in_waiting_id\",'',\"".$_REQUEST['pat_status']."\");
							top.iframeHome.iOLinkBookSheetFrameId.location.reload();
							
							var schedulerFrameObj = top.iframeHome.iOLinkScheduleFrameId;
							schedulerFrameObj.iOLink_change_month(schedulerFrameObj.document.getElementById('selected_month_number').value,schedulerFrameObj.document.getElementById('year_now').value,schedulerFrameObj.document.getElementById('surgeon_name_id').value);
						  </script>";
					//END RELOAD BOOKING SHEET
				}
			//}
		}else {
			unset($conditionArr);
			$conditionArr['patient_fname'] = addslashes(ucwords(trim($_POST["first_name"])));
			$conditionArr['patient_lname'] = addslashes(ucwords(trim($_POST["last_name"])));
			$conditionArr['date_of_birth'] = $patient_dob_temp;
			$conditionArr['zip'] = $_POST["zip"];
			$chkPatientDataTblDetails = $objManageData->getMultiChkArrayRecords('patient_data_tbl', $conditionArr);
			if($chkPatientDataTblDetails) { //IN CASE TO ADD PATIENT INFO
				echo "<script>alert('This patient has already been exist');</script>";
				foreach($chkPatientDataTblDetails as $patientDataTblDetails){
					$insertPatientDataId = $patientDataTblDetails->patient_id;
				}
			}else {
				$insertPatientDataId = $objManageData->addRecords($arrayPatientDataRecord, 'patient_data_tbl');
				echo "<script>
						alert('Patient Added');
						waitingPatient_info(\"$insertPatientDataId\",\"\");
					  </script>";
				$_REQUEST['pat_status']="";	  
			}
		}
	}	
}


//START VIEW RECORD OF WAITING PATIENT 

$patient_dos='';
$patient_dob='';
if($patient_id) {

	$patientDataTblQry = "SELECT * FROM `patient_data_tbl` WHERE patient_id='".$patient_id."'";
	$patientDataTblRes = imw_query($patientDataTblQry) or die(imw_error()); 
	$patientDataTblNumRow = imw_num_rows($patientDataTblRes);
	if($patientDataTblNumRow>0) {
		$patientDataTblRow = imw_fetch_array($patientDataTblRes);

		$title				 	= $patientDataTblRow['title'];
		$patient_first_name 	= $patientDataTblRow['patient_fname'];
		$patient_middle_name 	= $patientDataTblRow['patient_mname'];
		$patient_last_name 		= $patientDataTblRow['patient_lname'];
		$patient_suffix 		= $patientDataTblRow['patient_suffix'];
		$patient_name 			= $patient_last_name.", ".$patient_first_name;
		
		$patient_sex 			= $patientDataTblRow['sex'];
		
		$patient_address1		= $patientDataTblRow['street1'];
		$patient_address2 		= $patientDataTblRow['street2'];
		$patient_city 			= $patientDataTblRow['city'];
		$patient_state 			= $patientDataTblRow['state'];
		$patient_zip 			= $patientDataTblRow['zip'];
		$patient_home_phone 	= $patientDataTblRow['homePhone'];
		$patient_work_phone 	= $patientDataTblRow['workPhone'];
		$patient_religion 		= $patientDataTblRow['religion'];

		$patient_dob_temp 		= $patientDataTblRow['date_of_birth'];
		$patient_dob='';
		if($patient_dob_temp!=0) { $patient_dob = date('m-d-Y',strtotime($patient_dob_temp)); }
	
	}
	$AndPatientInWaitingTblQry=" AND pwt.patient_status!='Canceled' ";
	if($patient_in_waiting_id) { $AndPatientInWaitingTblQry = "AND patient_in_waiting_id='".$patient_in_waiting_id."'"; }
	
	$patientInWaitingTblQry = "SELECT pwt.*, st.comment_modified_status as scemr_comment_modified_status FROM `patient_in_waiting_tbl` pwt
								LEFT JOIN stub_tbl st ON(st.iolink_patient_in_waiting_id = pwt.patient_in_waiting_id AND st.iolink_patient_in_waiting_id !='0')
								WHERE pwt.patient_id='".$patient_id."' 
								AND pwt.patient_id!='' 
								AND pwt.dos>='".date('Y-m-d')."' 
								$AndPatientInWaitingTblQry
								ORDER BY pwt.dos ASC limit 0,1";
	$patientInWaitingTblRes = imw_query($patientInWaitingTblQry) or die(imw_error()); 
	$patientInWaitingTblNumRow = imw_num_rows($patientInWaitingTblRes);
	$futureAppt=true;
	if($patientInWaitingTblNumRow<=0) {
		$futureAppt=false;
		$patientInWaitingTblQry = "SELECT pwt.*, st.comment_modified_status as scemr_comment_modified_status FROM `patient_in_waiting_tbl` pwt
									LEFT JOIN stub_tbl st ON(st.iolink_patient_in_waiting_id = pwt.patient_in_waiting_id AND st.iolink_patient_in_waiting_id !='0')
									WHERE pwt.patient_id='".$patient_id."'  
									$AndPatientInWaitingTblQry
									ORDER BY pwt.dos DESC limit 0,1";
		$patientInWaitingTblRes = imw_query($patientInWaitingTblQry) or die(imw_error()); 
		$patientInWaitingTblNumRow = imw_num_rows($patientInWaitingTblRes);
	}
	if($patientInWaitingTblNumRow>0) {
		$patientInWaitingTblRow = imw_fetch_array($patientInWaitingTblRes);
		$patient_in_waiting_id 	= $patientInWaitingTblRow['patient_in_waiting_id'];
		$scemr_comment_modified_status = $patientInWaitingTblRow['scemr_comment_modified_status'];
		$patient_site 			= $patientInWaitingTblRow['site'];
		$surgeon_fname 			= trim($patientInWaitingTblRow['surgeon_fname']);
		$surgeon_mname 			= trim($patientInWaitingTblRow['surgeon_mname']);
		$surgeon_lname 			= trim($patientInWaitingTblRow['surgeon_lname']);
		if($surgeon_mname){ $surgeon_mname = ' '.$surgeon_mname; }
		$surgeon_name 			= $surgeon_fname.$surgeon_mname.' '.$surgeon_lname;

		$patient_dos_temp 		= $patientInWaitingTblRow['dos'];
		$patient_dos='';
		if($patient_dos_temp!=0) { $patient_dos = date('m-d-Y',strtotime($patient_dos_temp)); }

		$patient_prim_proc 	= stripslashes(trim($patientInWaitingTblRow['patient_primary_procedure']));
		$patient_sec_proc 	= stripslashes(trim($patientInWaitingTblRow['patient_secondary_procedure']));
		$patient_ter_proc 	= stripslashes(trim($patientInWaitingTblRow['patient_tertiary_procedure']));
		
		//$primaryPolicyNumber 	= $patientInWaitingTblRow['primaryPolicyNumber'];
		//$primaryGroupNumber 	= $patientInWaitingTblRow['primaryGroupNumber'];
		//$primaryPlaneName 	= $patientInWaitingTblRow['primaryPlaneName'];
		//$secondaryPolicyNumber= $patientInWaitingTblRow['secondaryPolicyNumber'];
		//$secondaryGroupNumber = $patientInWaitingTblRow['secondaryGroupNumber'];
		//$secondaryPlaneName 	= $patientInWaitingTblRow['secondaryPlaneName'];		
		$site 					= $patientInWaitingTblRow['site'];
		
		$patient_id 			= $patientInWaitingTblRow['patient_id'];

		$comment 				= $patientInWaitingTblRow['comment'];
		$iAscSyncroCount		= $patientInWaitingTblRow['iAscSyncroCount'];
		$commentReadOnly		= '';
		$commentBackground		= '';
		if($iAscSyncroCount>0) {
			//$commentReadOnly = "readonly";
			//$commentBackground = "background-color:#F0F0F0;";	
		}
		$pickup_time 			= $patientInWaitingTblRow['pickup_time'];
		$arrival_time 			= $patientInWaitingTblRow['arrival_time'];
		$surgery_time 			= $patientInWaitingTblRow['surgery_time'];
		$surgery_time_temp		= '';
		if($surgery_time && $surgery_time!='00:00:00') { $surgery_time_temp = date('h:i A',strtotime($surgery_time)); }
		
		if($mode=='search_patient' && $futureAppt==false) {
			$comment = $pickup_time = $arrival_time = $surgery_time_temp = "";			
		}
		$transportReq 			= $patientInWaitingTblRow['transportReq'];
		$transportReqComment 	= "";
		$chkTransportReqComment = stristr($comment,"Transportation Requested");
		if($transportReq=="1" && !$chkTransportReqComment) {
			$transportSpace="";
			if(trim($comment)) { $transportSpace = " "; }
			$transportReqComment = $transportSpace."Transportation Requested";	
		}
		
	}
}	
//END VIEW RECORD OF WAITING PATIENT

?>
<form name="frm_add_patient"  enctype="multipart/form-data" method="post" style="margin:0px; " action="iOLinkPtDetail.php" onSubmit="return validtion_pt_confirm();">
<?php
	if($patient_id) {
		$primaryScan1FlagImage 	 = "<img src='images/red_flag.png' width='12' height='14' border='0'>";
		$primaryScan2FlagImage 	 = "<img src='images/red_flag.png' width='12' height='14' border='0'>";
		$secondaryScan1FlagImage = "<img src='images/red_flag.png' width='12' height='14' border='0'>";
		$secondaryScan2FlagImage = "<img src='images/red_flag.png' width='12' height='14' border='0'>";
		$tertiaryScan1FlagImage = "<img src='images/red_flag.png' width='12' height='14' border='0'>";
		$tertiaryScan2FlagImage = "<img src='images/red_flag.png' width='12' height='14' border='0'>";

		if($primaryScan1Upload)   { $primaryScan1FlagImage   = "<img src='images/green_flag.png' style='cursor:pointer;' width='12' height='14' border='0'>";}
		if($primaryScan2Upload)   { $primaryScan2FlagImage   = "<img src='images/green_flag.png' style='cursor:pointer;' width='12' height='14' border='0'>";}
		if($secondaryScan1Upload) { $secondaryScan1FlagImage = "<img src='images/green_flag.png' style='cursor:pointer;' width='12' height='14' border='0'>";}
		if($secondaryScan2Upload) { $secondaryScan2FlagImage = "<img src='images/green_flag.png' style='cursor:pointer;' width='12' height='14' border='0'>";}
		if($tertiaryScan1Upload)  { $tertiaryScan1FlagImage  = "<img src='images/green_flag.png' style='cursor:pointer;' width='12' height='14' border='0'>";}
		if($tertiaryScan2Upload)  { $tertiaryScan2FlagImage  = "<img src='images/green_flag.png' style='cursor:pointer;' width='12' height='14' border='0'>";}
		//START CODE TO DEACTIVATE FUTURE ACTIVATED DATE OR EXPIRED DATE OF INSURANCE(IF EXIST)
		/*
		$curDate = strtotime(date('Y-m-d'));
		$updateInsActiveInsCompQry = "UPDATE insurance_data SET actInsComp='0' 
										WHERE patient_id = '".$patient_id."' 
										AND((active_date!='0000-00-00' AND active_date >  '".$curDate."')
											OR(active_date!='0000-00-00' AND active_date <= '".$curDate."' AND expiry_Date !='0000-00-00' AND expiry_Date <= '".$curDate."')
										   )";
		$updateInsActiveInsCompRes = imw_query($updateInsActiveInsCompQry) or die(imw_error());
		*/
		//END CODE TO DEACTIVATE FUTURE ACTIVATED DATE OR EXPIRED DATE OF INSURANCE(IF EXIST)
	
	}
?>

<?php $priRow = $objManageData->getInsurance($patient_in_waiting_id,'primary',$patient_id); 

	if($priRow){
		
	
?>

<div id="priInsDivVal" style="float:left; z-index:5; display:none; width:280px;top:200px; left:210px; margin-left:2px; margin-top:2px; position:absolute;  height:200px;">
	<table class=" table_pad_bdr alignCenter" style="border:none; width:99%; background-color:#FFFFFF;" >
		<tr class="text1b" >
            <td colspan="3" style="background-image:url(<?php echo $bgHeadingImage;?>);" >
                Ins. Primary
            </td>
        </tr>
		<tr class="text1" style="background-color:#F8F9F7;">
			<td class="alignLeft valignTop" style="background-color:#F1F4F0;">
				<?php echo $priRow['policy'];?>
			</td>
			<td class="alignLeft valignTop" style="background-color:#F1F4F0;">
				<?php echo $priRow['group_name'];?>
			</td>
			<td class="alignLeft valignTop" style="background-color:#F1F4F0;">
				<?php echo $priRow['plan_name'];?>
			</td>
		</tr>
		<tr class="label1" style="background-color:#F1F4F0;">
			<td class="alignLeft valignTop" style="background-color:#F1F4F0;">
				Policy#
			</td>
			<td class="alignLeft valignTop" style="background-color:#F1F4F0;">
				Group#
			</td>
			<td class="alignLeft valignTop nowrap" style="background-color:#F1F4F0;">
				Plan Name
			</td>
		</tr>
	</table>
</div>
<?php }else{ ?>
	<div id="priInsDivVal" style="float:left; z-index:500; background-color:#F1F4F0; display:none; width:280px;top:200px; left:210px; margin-left:2px; margin-top:2px; position:absolute;">
	<table class="table_pad_bdr alignCenter" style="border:none; width:99%; background-color:#FFFFFF; ">
		<tr class="text1b" >
            <td style="background-image:url(<?php echo $bgHeadingImage;?>);" >
                Ins. Primary
            </td>
        </tr>
        <tr class="text1b" style="background-color:#F1F4F0;">
				<td>	
					<span class="text1"  >No result available</span>
				</td>
			</tr>
	</table>
</div>
	<?php } ?>
	
	<?php $secRow = $objManageData->getInsurance($patient_in_waiting_id,'secondary',$patient_id); 

	if($secRow){ ?>
	<div id="secInsDivVal" style="width:280px; margin-left:2px; margin-top:2px; display:none; z-index:5; top:200px; left:350px; position:absolute;" onmouseout="showIns('secInsDiv')">
		<table class=" table_pad_bdr alignCenter" style="border:none; width:99%; background-color:#FFFFFF;" >
			<tr class="text1b" >
                <td colspan="3" style="background-image:url(<?php echo $bgHeadingImage;?>);" >
                    Ins. Secondary
                </td>
            </tr>
            <tr class="text1" style="background-color:#F8F9F7;">
                <td class="alignLeft valignTop" style="background-color:#F1F4F0;">
                    <?php echo $secRow['policy'];?>
                </td>
                <td class="alignLeft valignTop" style="background-color:#F1F4F0;">
                    <?php echo $secRow['group_name'];?>
                </td>
                <td class="alignLeft valignTop" style="background-color:#F1F4F0;">
                    <?php echo $secRow['plan_name'];?>
                </td>
            </tr>
            <tr class="label1" style="background-color:#F1F4F0;">
                <td class="alignLeft valignTop" style="background-color:#F1F4F0;">
                    Policy#
                </td>
                <td class="alignLeft valignTop" style="background-color:#F1F4F0;">
                    Group#
                </td>
                <td class="alignLeft valignTop nowrap" style="background-color:#F1F4F0;">
                    Plan Name
                </td>
            </tr>
		</table>
	</div>
	<?php } else{?>
			<div id="secInsDivVal" style="float:left; z-index:5; background-color:#F1F4F0; display:none; width:280px;top:200px; left:350px; margin-left:2px; margin-top:2px; position:absolute;">
			<table class="table_pad_bdr alignCenter" style="border:none; width:99%; background-color:#FFFFFF;">
                <tr class="text1b" >
                    <td style="background-image:url(<?php echo $bgHeadingImage;?>);" >
                        Ins. Secondary
                    </td>
                </tr>
                <tr class="text1b" style="background-color:#F1F4F0;">
                        <td>	
                            <span class="text1" >No result available</span>
                        </td>
                    </tr>
            </table>
		</div>
	<?php } ?>
	
	<?php $terRow = $objManageData->getInsurance($patient_in_waiting_id,'tertiary',$patient_id); 

	if($terRow){ ?>
	<div id="terInsDivVal" style="width:280px; margin-left:2px; margin-top:2px; display:none; z-index:5; top:200px; left:460px; position:absolute;" onmouseout="showIns('terInsDiv')">
		<table class=" table_pad_bdr alignCenter" style="border:none; width:99%; background-color:#FFFFFF;" >
			<tr class="text1b" >
                <td colspan="3" style="background-image:url(<?php echo $bgHeadingImage;?>);" >
                    Ins. Tertiary
                </td>
            </tr>
            <tr class="text1b" style="background-color:#F8F9F7;">
                <td class="alignLeft valignTop" style="background-color:#F1F4F0;">
                    <?php echo $terRow['policy'];?>
                </td>
                <td class="alignLeft valignTop" style="background-color:#F1F4F0;">
                    <?php echo $terRow['group_name'];?>
                </td>
                <td class="alignLeft valignTop" style="background-color:#F1F4F0;">
                    <?php echo $terRow['plan_name'];?>
                </td>
            </tr>
        	<tr class="label1" style="background-color:#F1F4F0;">
                <td class="alignLeft valignTop" style="background-color:#F1F4F0;">
                    Policy#
                </td>
                <td class="alignLeft valignTop" style="background-color:#F1F4F0;">
                    Group#
                </td>
                <td class="alignLeft valignTop nowrap" style="background-color:#F1F4F0;">
                    Plan Name
                </td>
            </tr>
		</table>
	</div>
	<?php } else{?>
			<div id="terInsDivVal" style="float:left; z-index:5; background-color:#F1F4F0; display:none; width:280px;top:200px; left:460px; margin-left:2px; margin-top:2px; position:absolute;">
			<table class="table_pad_bdr alignCenter" style="border:none; width:99%; background-color:#FFFFFF;">
                <tr class="text1b" >
                    <td  style="background-image:url(<?php echo $bgHeadingImage;?>);" >
                        Ins. Tertiary
                    </td>
                </tr>
                <tr class="text1b" style="background-color:#F1F4F0;">
                        <td>	
                            <span class="text1"  >No result available</span>
                        </td>
                    </tr>
            </table>
		</div>
	<?php } ?>	
    <div style="position:absolute; margin:0px 285px; display:none;" id="load_div"><img src="images/ajax-loader5.gif" /></div>
 <table class="table_pad_bdr alignLeft" style="border:solid 1px; border-color:#9FBFCC; background-color:#ECF1EA; width:755px; height:327px;" onDblClick="closePreDefineDiv()" onMouseOver="closePreDefineDiv();">
	<tr class="valignTop">
		<td class="valignTop">
            <input type="hidden" id="patient_in_waiting_id" name="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
            <input type="hidden" id="iasc_facility_id" name="iasc_facility_id" value="<?php echo $iasc_facility_id; ?>">            
            <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $patient_id; ?>">
            <input type="hidden" name="pt_wait_book" id="pt_wait_book" value="">
            <input type="hidden" name="pt_wait_move" id="pt_wait_move" value="">
            <input type="hidden" name="inCorrectZipCode" id="inCorrectZipCode" value="">
            <input type="hidden" name="pt_wait_submit" id="pt_wait_submit" value="Save">
			<input type="hidden" name="pat_booking_id" id="pat_booking_id">
            <input type="hidden" name="pat_status" style="border:1px solid #F00;" id="pat_status" value="<?php echo $_REQUEST['pat_status']; ?>">
            <input type="hidden" id="iAscSyncroCount" name="iAscSyncroCount" value="<?php echo $iAscSyncroCount; ?>">
            <input type="hidden" name="scemr_comment_modified_status" id="scemr_comment_modified_status" value="<?php echo $scemr_comment_modified_status;?>">
            <input type="hidden" name="scemr_comment_status_to_reset" id="scemr_comment_status_to_reset" value="">
            <table class="alignLeft" style="width:100%; border:none; padding:0px;">
                <tr>
                    <td>
                        <table class="tblBg table_pad_bdr" style="width:99%; padding:10px; border:none;" >
                            <tr >
                                <td class="valignTop" style="padding:5px; padding-left:3px;">
                                    <table class="table_pad_bdr alignLeft" style="border:none; width:99%;"  >
                                        <tr class="text_smallb ">
                                            <td colspan="5" class="alignLeft" style="padding:0px;padding-left:2px;"><span class="text_smallb" style="font-size:12px;background:background-color:#F1F4F0;">Name  <?php if($transportReq=="1") {?><img src="images/bus_icon2.png" style="height:13px; width:20px;" alt="Transportation Requested" title="Transportation Requested"><?php } ?></span></td>
                                            <td class="alignLeft" style="padding:0px;"><span class="text_smallb" style="font-size:12px;background:background-color:#F1F4F0;">Sex</span></td>
                                            <td class="alignLeft" style="padding:0px;"><span class="text_smallb" style="font-size:12px;background:background-color:#F1F4F0;">DOB</span></td>
                                        </tr>
                                        <tr class="text_smallb" >
                                            <td class="padd0"  style="width:60px;">													
                                                <select name="title" tabindex="1" class="field text1" style="width:50px;  border:1px solid #B9B9B9;  height:22px;">
                                                    <option value="" <?php if($title==""){echo("selected");} ?>></option>
                                                    <option value="Mr." <?php if($title=="Mr."){echo("selected");}?>>Mr.</option>
                                                    <option value="Mrs." <?php if(($title=="married" && $patient_sex=="f")||$title=="Mrs."){echo("selected");}?>>Mrs.</option>
                                                    <option value="Ms." <?php if($title=="Ms."){echo("selected");}?>>Ms.</option>
                                                    <option value="Dr." <?php if($title=="Dr."){echo("selected");}?>>Dr.</option>
                                                </select><div class="label1 alignLeft" >Title</div>
                                            </td >
                                            <td class="alignLeft valignTop padd0" style="width:193px; padding-left:2px;">
                                                <input type="text" id="first_name_id" name="first_name" onblur="myTrim(this);" onFocus="changeTxtGroupColor(1,'first_name_id');" onKeyUp="changeTxtGroupColor(1,'first_name_id');" style=" <?php if(trim(!$patient_first_name)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;border:1px solid #B9B9B9;  height:17px; width:183px;" class="field text1 capitalize" tabindex="2" value="<?php echo $patient_first_name;?>" /><div class="label1 text_small">First Name</div>
                                            </td >
                                            <td class="alignLeft valignTop padd0" style="width:50px;">
                                                <input type="text" id="middle_name_id" name="middle_name" onblur="myTrim(this);" style=" border:1px solid #B9B9B9;  height:17px; width:40px;" class="field text1 capitalize"  tabindex="3" value="<?php echo $patient_middle_name;?>" /><div class="label1">MI</div>
                                            </td>
                                            <td class="alignLeft valignTop padd0" style="width:193px;">
                                                <input type="text" id="last_name_id" name="last_name" onblur="myTrim(this)" onFocus="changeTxtGroupColor(1,'last_name_id');" onKeyUp="changeTxtGroupColor(1,'last_name_id');" style=" <?php if(trim(!$patient_last_name)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;border:1px solid #B9B9B9;  height:17px;width:183px;"  class="field text1 capitalize" tabindex="4" value="<?php echo $patient_last_name;?>" /><div class="label1">Last Name</div>
                                            </td>
                                            <td class="alignLeft valignTop padd0" style="width:50px;">
                                                <input name="patient_suffix" id="patient_suffix" onblur="myTrim(this);" type="text" class="field text1 capitalize" tabindex="5" style=" border:1px solid #B9B9B9; width:40px;  height:17px;" value="<?php echo $patient_suffix;?>" ><div class="label1">Sfx</div>								
                                            </td>
                                            <td class="alignLeft valignTop padd0" style="width:60px;">
                                                <select name="sex_list" id="sex_list_id" tabindex="6" class="field text1" onchange="changeTxtGroupColor(1,'sex_list_id');" onFocus="changeTxtGroupColor(1,'sex_list_id');" onKeyUp="changeTxtGroupColor(1,'sex_list_id');" style=" <?php if(trim(!$patient_sex)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;border:1px solid #B9B9B9;  height:22px; width:50px;">
                                                    <option value=""></option>
                                                    <option value="m" <?php if($patient_sex=="m") { echo "selected"; }?> >M</option>
                                                    <option value="f" <?php if($patient_sex=="f") { echo "selected"; }?> >F</option>
                                                </select>
                                            </td>
                                            <td class="alignLeft valignTop nowrap padd0">
                                                <input type="hidden" id="bp" name="bp_hidden">
                                                <input type="hidden"  id="rowK" name="row" value="">
                                                <input type="text" id="bp_temp" name="dob" onBlur="checkdate(this);"  class="field text1" maxlength="10" onFocus="changeTxtGroupColor(1,'bp_temp');" onKeyUp="changeTxtGroupColor(1,'bp_temp');if(event.keyCode=='13') {this.blur(); }" style=" <?php if(trim(!$patient_dob)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;border:1px solid #B9B9B9; width:75px; height:17px;" tabindex="7" value="<?php echo $patient_dob;?>" />
                                                <img style="cursor:pointer;position:relative; top:2px;width:20px; height:20px; " alt="DOB" onClick="newWindow('bp_temp')" src="images/icon_cal.jpg" >
                                            </td>
                                        </tr>
                                        
                                    </table>
                                </td>		
                            </tr>
                            <tr >
                                <td class="valignTop" style="padding:5px; padding-left:3px;background-color:#FFFFFF;">
                                    <table class="alignCenter" style="border:none; width:99%; padding:0px;">
                                        <tr class="text_smallb valignTop" style="background-color:#FFFFFF;">
										<?php 
											$addressFldWidth = ( $show_religion ) ? '200' : '235';
											if( $show_religion ) {
										?>		
											<td class="alignLeft valignTop padd0"  >
                                                <input name="religion" id="religion" type="text" class="field text1 capitalize" tabindex="8" style="font-family:verdana; border:1px solid #B9B9B9;  height:17px; width:70px; " value="<?php echo stripslashes($patient_religion);?>" ><div class="label1 alignLeft">Religion</div>
											</td>
										<?php } ?>	
                                            <td class="alignLeft valignTop padd0"  >
                                                <input name="address1_id" id="address1" type="text" class="field text1 capitalize" tabindex="8" style="font-family:verdana; border:1px solid #B9B9B9;  height:17px; width:<?php echo $addressFldWidth;?>px; " value="<?php echo stripslashes($patient_address1);?>" ><div class="label1 alignLeft" >Address1</div>
                                            </td>
                                            <td class="alignLeft valignTop padd0" style=" padding-left:5px;" >
                                                <input name="address2_id" id="address2" type="text" class="field text1 capitalize" tabindex="9" style="font-family:verdana; border:1px solid #B9B9B9;  height:17px; width:<?php echo $addressFldWidth;?>px; " value="<?php echo stripslashes($patient_address2);?>" ><div class="label1 alignLeft" >Address2</div>
                                            </td>
                                            <td class="alignLeft valignTop padd0" style=" padding-left:5px;">
                                                <input type="text" id="bp_temp7" name="home_phone" class="field text1" maxlength="12" onBlur="ValidatePhone(this);" style="border:1px solid #B9B9B9;  height:17px; width:120px;" tabindex="10" value="<?php echo $patient_home_phone;?>" /><div class="label1 alignLeft" >Home Phone#</div>
                                            </td>
                                            <td class="alignLeft valignTop padd0" style=" padding-left:5px;">
                                                <input type="text" id="work_phone" name="work_phone" class="field text1" maxlength="12" onBlur="ValidatePhone(this);" style=" border:1px solid #B9B9B9;  height:17px; width:120px;" tabindex="10" value="<?php echo $patient_work_phone;?>" /><div class="label1 alignLeft" >Work Phone#</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr >
                                <td class="alignLeft" style="padding:5px; padding-left:3px;">
                                    <table style="border:none; width:100%; ">
                                        <tr class="text_smallb alignLeft">
                                            <td class="alignLeft valignTop padd0" style="width:65px; ">
                                                <input type="text" id="bp_temp6" name="zip" class="field text1" onFocus="changeTxtGroupColor(1,'bp_temp6');" onKeyUp="changeTxtGroupColor(1,'bp_temp6');" style=" <?php if(trim(!$patient_zip)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;border:1px solid #B9B9B9;white-space:nowrap; height:17px; width:55px;" tabindex="11" value="<?php echo $patient_zip;?>" onBlur="return getCityStateFn(this,document.getElementById('city'),document.getElementById('state'));"  /><div class="label1 alignLeft" >Zip</div>
                                            </td>
                                            <td class="alignLeft valignTop padd0" style="width:188px; padding-left:5px; ">
                                              <input type="text" id="city" name="city" class="field text1 capitalize" onFocus="changeTxtGroupColor(1,'city');" onKeyUp="changeTxtGroupColor(1,'city');" style=" <?php if(trim(!$patient_city)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;border:1px solid #B9B9B9;white-space:nowrap;  height:17px; width:178px; margin-left:1px;" tabindex="12" value="<?php echo stripslashes($patient_city);?>"  /><div class="label1 alignLeft" >City</div>
                                            </td>
                                            <td class="alignLeft valignTop padd0" style="width:50px; padding-left:7px; ">
                                                <input type="text" id="state" name="state" class="field text1 capitalize" onFocus="changeTxtGroupColor(1,'state');" onKeyUp="changeTxtGroupColor(1,'state');" style=" <?php if(trim(!$patient_state)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;border:1px solid #B9B9B9;white-space:nowrap; height:17px; width:40px; " tabindex="13" value="<?php echo stripslashes($patient_state);?>"  /><div class="label1 alignLeft" >State</div>
                                                <span id="ZipAjaxLoadId" style="position:absolute;padding:0px; top:100px; left:175px; "></span>
                                            </td>
                                            <td class="alignRight valignTop padd0" style="padding-right:15px; width:26%; ">
                                                <a href="#" class="linkNew " style="font-size:12px;" onclick="showIns('<?php echo $patient_id; ?>','<?php echo $patient_in_waiting_id; ?>','primary')" onmouseover="showIns('priInsDivVal');" onmouseout="showIns('priInsDivVal');"> Primary Ins.</a>
                                            </td>
                                            <td class="alignLeft valignTop padd0" style="width:1%; background-color:#F1F4F0;"></td>
                                            <td class="alignCenter valignTop padd0" style="padding-right:15px; width:19%; ">
                                                <a href="#" class="linkNew" style="font-size:12px;" onclick="showIns('<?php echo $patient_id; ?>','<?php echo $patient_in_waiting_id; ?>','secondary')" onmouseover="showIns('secInsDivVal');" onmouseout="showIns('secInsDivVal');">Secondary Ins.</a>
                                            </td>
                                            <td class="alignCenter valignTop padd0" style="width:19%;">
                                                <a href="#" class="linkNew" style="font-size:12px;" onclick="showIns('<?php echo $patient_id; ?>','<?php echo $patient_in_waiting_id; ?>','tertiary')" onmouseover="showIns('terInsDivVal');" onmouseout="showIns('terInsDivVal');">Tertiary Ins.</a>
                                            </td>
                                        </tr>
                                        
                                  </table>
                                </td>
                            </tr>
                            <tr class="text_smallb alignLeft">
                                <td style="padding:5px; padding-left:3px;background-color:#FFFFFF; ">
                                    <table class="table_collapse alignLeft padd0" style="width:99%;">
										
                                        <tr style="background-color:#FFFFFF;">
                                            <td class="alignLeft valignTop padd0" style="font-size:12px;">Booking</td>
                                                
                                            <td class="alignLeft valignTop padd0">&nbsp;</td>
                                            <td class="alignLeft valignTop padd0">&nbsp;</td>
                                            <td class="alignLeft valignTop padd0">&nbsp;</td>
                                        </tr>
										<tr style="background-color:#FFFFFF;">
                                            <td class="alignLeft valignTop padd0">
                                                <select name="surgeon_name_id" id="surgeon_name_id" class="field text1" onchange="changeTxtGroupColor(1,'surgeon_name_id');" onFocus="changeTxtGroupColor(1,'surgeon_name_id');" onKeyUp="changeTxtGroupColor(1,'surgeon_name_id');" style=" <?php if(trim(!$surgeon_name)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;width:180px; height:22px; border:1px solid #B9B9B9;" tabindex="14">
                                                    <?php
                                                   	 	$getSurgeosDetails=array();
														$strQuery1Part="";
														if($coordinatorType!='Master') { //DISPLAY ALL SUGEON FOR MASTER COORDINATOR
															$strQuery1Part=getPracticeUser($practiceName,"AND");   
														}
														$qrySurgeonDetail="Select * FROM users Where user_type='Surgeon' ".$strQuery1Part." ORDER BY lname ASC";
														$resSurgeonDetail=imw_query($qrySurgeonDetail)or die(imw_error());
														if(imw_num_rows($resSurgeonDetail)>0){
															while($rowSurgeosDetails=imw_fetch_object($resSurgeonDetail)){
																$getSurgeosDetails[]=$rowSurgeosDetails;
															}
														}
			                                        //$getSurgeosDetails = $objManageData->getMultiChkArrayRecords('users', $conditionArr, 'lname','ASC');
										            if(count($getSurgeosDetails)>=2 || !$getSurgeosDetails) {//IF NO SURGEON EXIST OR MORE THAN ONE SURGEON EXIST THEN DISPLAY THIS BLANK OPTION
                                                    ?>
                                                        <option value="">Select Any</option>
                                                    <?php
                                                    }
                                                    if($getSurgeosDetails) {
                                                        foreach($getSurgeosDetails as $surgeonsList){
                                                            $usersId = $surgeonsList->usersId;
                                                            $surgeonFname = trim($surgeonsList->fname);
                                                            $surgeonLname = trim($surgeonsList->lname);
                                                            $surgeonMname = trim($surgeonsList->mname);
                                                            if($surgeonMname) {
                                                                $surgeonMname = ' '.$surgeonMname;
                                                            }
                                                            $surgeonName = $surgeonFname.$surgeonMname.' '.$surgeonLname;
                                                            $surgeon_deleteStatus = $surgeonsList->deleteStatus;
                                                            if($surgeon_deleteStatus=="Yes") {
                                                            }else{
                                                                $surgeonSelected="";
                                                                if($surgeon_name == trim($surgeonName)) {
                                                                    $surgeonSelected = "selected";
                                                                }
                                                            ?>
                                                                <option value="<?php echo $usersId; ?>" <?php echo $surgeonSelected;?>><?php echo stripslashes($surgeonLname.', '.$surgeonFname.' '.$surgeonMname); ?></option>
                                                            <?php
                                                            }
                                                        }
                                                    }	
                                                    ?>
                                                </select><div class="label1">Surgeon Name</div>
                                            </td>
                                            <td class="alignLeft valignTop padd0">
                                                <select name="prim_proc" id="prim_proc" class="field text1"  onchange="changeTxtGroupColor(1,'prim_proc');" onFocus="changeTxtGroupColor(1,'prim_proc');" onKeyUp="changeTxtGroupColor(1,'prim_proc');" style=" <?php if(trim(!$patient_prim_proc)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;width:175px;height:22px;border:1px solid #B9B9B9; vertical-align:top;" tabindex="15">
                                                    <option value="">Select Any</option>
                                                    <?php
                                                    $getProcedureDetails = imw_query("select * from  procedures order by `name` asc, del_status desc");
                                                    while($PrimaryProcedureList=@imw_fetch_array($getProcedureDetails)){
                                                        $ProcedureId = $PrimaryProcedureList['procedureId'];
                                                        $ProcedureCatergoryId = $PrimaryProcedureList['catId'];
                                                        $ProcedureName = stripslashes(trim($PrimaryProcedureList['name']));
                                                        $ProcedureAliasName = stripslashes(trim($PrimaryProcedureList['procedureAlias']));
														$priDelStatus = trim($PrimaryProcedureList['del_status']);
                                                        if($ProcedureCatergoryId=='2'){
                                                            $category_display="(LP)&nbsp;";
                                                        }
                                                        else{
                                                            $category_display="";
                                                        }
                                                        $ProcedureAliasNameDisplay = $ProcedureAliasName;
														if( constant('SYNC_PROC_WITH_IMW') == 'YES'){
															$ProcedureAliasNameDisplay .= ($ProcedureAliasNameDisplay?' - ':'').$ProcedureName;
														}
														else {	
															if(!$ProcedureAliasName) { $ProcedureAliasNameDisplay = $ProcedureName;}
														}

                                                        $priSel = "";
														if($patient_prim_proc && (strtolower($patient_prim_proc)==strtolower($ProcedureName) || strtolower($patient_prim_proc)==strtolower($ProcedureAliasName))) { 
															$priSel = "selected"; 
														}
														if(!$priSel && $priDelStatus=="yes") {
															continue;	
														}
														?>
                                                            <option title="<?php echo $ProcedureName; ?>" value="<?php echo $ProcedureName; ?>" <?php echo $priSel;?> ><?php echo $category_display.$ProcedureAliasNameDisplay;?></option>
                                                        <?php	  
                                                        
                                                    }
                                                    ?>
                                                </select><div class="label1">Primary Procedure</div>
                                            </td>
                                            <td class="alignLeft valignTop padd0">
                                                <select name="sec_proc" class="field text1"  style="width:175px;height:22px;border:1px solid #B9B9B9; vertical-align:top;" tabindex="16">
                                                    <option value="">Select Any</option>
                                                    <?php
                                                    $getProcedureDetails = imw_query("select * from  procedures order by `name` asc, del_status desc");
                                                    while($PrimaryProcedureList=@imw_fetch_array($getProcedureDetails)){
                                                        $ProcedureId = $PrimaryProcedureList['procedureId'];
                                                        $ProcedureCatergoryId = $PrimaryProcedureList['catId'];
                                                        $ProcedureName = stripslashes(trim($PrimaryProcedureList['name']));
                                                        $ProcedureAliasName = stripslashes(trim($PrimaryProcedureList['procedureAlias']));
                                                        $secDelStatus = trim($PrimaryProcedureList['del_status']);
														if($ProcedureCatergoryId=='2'){
                                                            $category_display="(LP)&nbsp;";
                                                        }
                                                        else{
                                                            $category_display="";
                                                        }
                                                        $secProcedureAliasNameDisplay = $ProcedureAliasName;
														if( constant('SYNC_PROC_WITH_IMW') == 'YES'){
															$secProcedureAliasNameDisplay .= ($secProcedureAliasNameDisplay?' - ':'').$ProcedureName;
														}
														else {	
															if(!$ProcedureAliasName) { $secProcedureAliasNameDisplay = $ProcedureName;}
														}
                                                        
														$secSel = "";
														if($patient_sec_proc && (strtolower($patient_sec_proc)==strtolower($ProcedureName) || strtolower($patient_sec_proc)==strtolower($ProcedureAliasName))) { 
															$secSel = "selected"; 
														}
														if(!$secSel && $secDelStatus=="yes") {
															continue;	
														}
														
														?>
                                                        <option title="<?php echo $ProcedureName; ?>" value="<?php echo $ProcedureName; ?>" <?php echo $secSel; ?> ><?php echo $category_display.$secProcedureAliasNameDisplay;?></option>
                                                        <?php	  
                                                    }
                                                    ?>
                                                </select><div class="label1">Secondary Procedure</div>
                                            </td>
                                            <td class="alignLeft valignTop padd0">
                                                <select name="ter_proc" class="field text1"  style="width:175px;height:22px;border:1px solid #B9B9B9; vertical-align:top;" tabindex="16">
                                                    <option value="">Select Any</option>
                                                    <?php
                                                    $getProcedureDetails = imw_query("select * from  procedures order by `name` asc, del_status desc");
                                                    while($PrimaryProcedureList=@imw_fetch_array($getProcedureDetails)){
                                                        $ProcedureId = $PrimaryProcedureList['procedureId'];
                                                        $ProcedureCatergoryId = $PrimaryProcedureList['catId'];
                                                        $ProcedureName = stripslashes(trim($PrimaryProcedureList['name']));
                                                        $ProcedureAliasName = stripslashes(trim($PrimaryProcedureList['procedureAlias']));
                                                        $terDelStatus = trim($PrimaryProcedureList['del_status']);
														if($ProcedureCatergoryId=='2'){
                                                            $category_display="(LP)&nbsp;";
                                                        }
                                                        else{
                                                            $category_display="";
                                                        }
                                                        $terProcedureAliasNameDisplay = $ProcedureAliasName;
														if( constant('SYNC_PROC_WITH_IMW') == 'YES'){
															$terProcedureAliasNameDisplay .= ($terProcedureAliasNameDisplay?' - ':'').$ProcedureName;
														}
														else {	
															if(!$ProcedureAliasName) { $terProcedureAliasNameDisplay = $ProcedureName;}
														}

                                                        $terSel = "";
														if($patient_ter_proc && (strtolower($patient_ter_proc)==strtolower($ProcedureName) || strtolower($patient_ter_proc)==strtolower($ProcedureAliasName))) { 
															$terSel = "selected"; 
														}
														if(!$terSel && $terDelStatus=="yes") {
															continue;	
														}
                                                        
														?>
                                                        <option title="<?php echo $ProcedureName; ?>" value="<?php echo $ProcedureName; ?>" <?php echo $terSel; ?> ><?php echo $category_display.$terProcedureAliasNameDisplay;?></option>
                                                        <?php	  
                                                    }
                                                    ?>
                                                </select><div class="label1">Tertiary Procedure</div>
                                            </td>
                                        </tr>
                                    </table>
								</td>
							</tr>
                            <tr>
                                <td class="valignTop" style="padding-left:2px;">
                                    <table class="alignCenter" style="border:none; width:99%; padding:0px;">
                                        <tr  class="alignLeft">
                                            <td class="padd0" style="width:15%;" >
                                                <input type="checkbox" tabindex="17" id="transportReq" name="transportReq" <?php if($transportReq=="1") { echo "Checked";  } ?> value="1"/><div class="label1">Transport</div>
                                            </td>
                                            <td class="text_smallb padd0" style="width:20%;">
                                                <input type="text" id="bp_temp2" name="dos" class="field text1" maxlength="10"  onFocus="changeTxtGroupColor(1,'bp_temp2');" onblur="checkdate(this,'currentCase');"  onKeyUp="changeTxtGroupColor(1,'bp_temp2');if(event.keyCode=='13') {this.blur(); }" style=" <?php if(trim(!$patient_dos)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;border:1px solid #B9B9B9; width:75px; height:17px; " tabindex="18" value="<?php echo $patient_dos;?>" /><span style="padding-left:7px;"><img onClick="newWindow('bp_temp2')" src="images/icon_cal.jpg" alt="DOS" style="cursor:pointer; width:20px; height:20px; position:relative; right:5px; top:2px; "   ></span><div class="label1">DOS</div>
                                            	<input type="hidden" name="hidd_dos" id="hidd_dos" value="<?php echo $patient_dos;?>" />
                                            </td>
                                            <td class="text_smallb padd0" style="width:22%;">
                                                <select name="site" id="site" class="field text1"  onchange="changeTxtGroupColor(1,'site');" onFocus="changeTxtGroupColor(1,'site');" onKeyUp="changeTxtGroupColor(1,'site');" style=" <?php if(trim(!$site)){ echo $chngBckGroundColor;}else {  echo $whiteBckGroundColor;}?>;width:140px; height:22px; border:1px solid #B9B9B9;" tabindex="19">
                                                    <option value=""></option>
                                                    <option value="left"  <?php if($site=='left') { echo 'selected'; }?>>Left</option>
                                                    <option value="right" <?php if($site=='right') { echo 'selected'; }?>>Right</option>
                                                    <option value="both"  <?php if($site=='both') { echo 'selected'; }?>>Bilateral</option>
                                                    <option value="left upper lid" 	<?php if($site=='left upper lid') 	{ echo 'selected'; }?>>Left Upper Lid</option>
                                                    <option value="left lower lid" 	<?php if($site=='left lower lid') 	{ echo 'selected'; }?>>Left Lower Lid</option>
                                                    <option value="right upper lid" <?php if($site=='right upper lid') 	{ echo 'selected'; }?>>Right Upper Lid</option>
                                                    <option value="right lower lid" <?php if($site=='right lower lid')	{ echo 'selected'; }?>>Right Lower Lid</option>
                                                    <option value="bilateral upper lid" <?php if($site=='bilateral upper lid') 	{ echo 'selected'; }?>>Bilateral Upper Lid</option>
                                                    <option value="bilateral lower lid" <?php if($site=='bilateral lower lid')	{ echo 'selected'; }?>>Bilateral Lower Lid</option>
                                                </select><div class="label1">Site</div>
                                            </td>
                                            <td class="text_smallb padd0" style="width:21%;">
                                                <input type="text" id="bp_temp4" name="pickup_time" class="field text1"  maxlength="10" onblur="javascript:chkTmFormat(this);" style=" border:1px solid #B9B9B9; height:17px; width:80px; " 	onkeyup="if(event.keyCode=='13') {this.blur();}" tabindex="20" value="<?php echo $pickup_time;?>" /><div class="label1">Pick up TIme</div>
                                            </td>
                                            <td class="text_smallb padd0" style="width:20%;">
                                                <input type="text" id="bp_temp3" name="arrival_time" class="field text1" maxlength="10" onblur="javascript:chkTmFormat(this);" style=" border:1px solid #B9B9B9; height:17px; width:80px; " onkeyup="if(event.keyCode=='13') {this.blur();}" tabindex="21" value="<?php echo $arrival_time;?>" /><div class="label1">Arrival TIme</div>
                                            </td>
                                            <td class="text_smallb padd0" style="width:20%;">
                                                <input type="text" id="bp_temp5" title="<?php if($surgery_time_temp) {echo $surgery_time_temp;}?>" name="surgery_time" maxlength="8" class="field text1" onblur="javascript:chkTmFormat(this);" style=" border:1px solid #B9B9B9; height:17px; width:80px;" 	onkeyup="if(event.keyCode=='13') {this.blur();}"  tabindex="22" value="<?php if($surgery_time_temp) {echo $surgery_time_temp;}?>"/><div class="label1">S.TIme</div>
                                            </td>
                                        </tr>
                                        
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="table_collapse" style="border:none; background-color:#FFFFFF;">	
                                        <tr class="text_smallb alignLeft"><td><span class="text_smallb"  style="font-size:12px;">Comment</span></td></tr>													
                                        <tr style="background-color:#FFFFFF;">
                                            <td class="text_smallb">
                                            	<textarea id="iOLinkCommentId" name="comment"  class="field text1" style="font-family:verdana; border:1px solid #B9B9B9;  height:45px; width:730px; " tabindex="23"  ><?php echo trim(stripslashes($comment).$transportReqComment);?></textarea>
                                            	<input type="hidden" name="hidd_comment" id="hidd_comment" value="<?php echo trim(stripslashes($comment).$transportReqComment);?>">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>                                        
						</table>
					</td>
				</tr>                            
            </table>
		</td>
	</tr>
</table>
</form>
