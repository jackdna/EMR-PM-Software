<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
//FUNCTION EDIT BY SURINDER 
function dateDiffCommon($dformat, $endDate, $beginDate){
	$date_parts1=explode($dformat, $beginDate);
	$date_parts2=explode($dformat, $endDate);
	$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
	$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
	return $end_date - $start_date;}

//END  FUNCTION EDIT BY SURINDER

function getPracticeName($loginUserId,$uType) {
	$practiceNameCord='';
	if($loginUserId) {
		//$userTypeQry = "SELECT practiceName FROM users WHERE  usersId = '$loginUserId' and user_type='$uType'";
		$userTypeQry = "SELECT practiceName FROM users WHERE  usersId = '$loginUserId'";
		$userTypeRes = imw_query($userTypeQry);
		$userTypeRows = imw_fetch_array($userTypeRes);
		$practiceNameCord = $userTypeRows['practiceName'];
	}
	return $practiceNameCord;
}
function getPracticeUser($practiceName,$andCond,$usrDot="") {
	$strQuery1Part=$usrDotCon='';
	$fieldName = "practiceName";
	if($usrDot){
		$fieldName=$usrDot.".practiceName";	
	}
	if($practiceName){
		$practiceNameArr = explode(",",$practiceName);
		foreach($practiceNameArr as $prNme){
			//$strQuery1Part .= " '".$prNme."' IN($fieldName) OR ";
			$strQuery1Part .= " $fieldName REGEXP '[[:<:]]".$prNme."[[:>:]]' OR ";
		}
		if($strQuery1Part != ''){
			$strQuery1Part = substr($strQuery1Part,0,-4);
			$strQuery1Part = " ".$andCond." (".$strQuery1Part.")";
		}
	}else {
		$strQuery1Part = " '' = $fieldName";	
		$strQuery1Part = " ".$andCond." (".$strQuery1Part.")";
	}
	return $strQuery1Part;
}
function getSurgeonNameArray($loginUserId) {
	$surgeonNmeArr=array();
	if($loginUserId) {
		$userTypeQry = "SELECT fname,mname,lname,npi FROM users WHERE  usersId = '".$loginUserId."'";
		$userTypeRes = imw_query($userTypeQry) or die(imw_error());
		$userTypeRows = imw_fetch_array($userTypeRes);
		$surgeonNmeArr[0] = $userTypeRows['fname'];
		$surgeonNmeArr[1] = $userTypeRows['mname'];
		$surgeonNmeArr[2] = $userTypeRows['lname'];
		$surgeonNmeArr[3] = $userTypeRows['npi'];
	}
	return $surgeonNmeArr;
}

function getiAscUserId($fname,$mname,$lname,$npi='') {
	$userId = '';
	$npi = trim($npi);
	$andUserIdQry = " AND  fname = '".addslashes($fname)."' AND mname = '".addslashes($mname)."' AND lname = '".addslashes($lname)."' ";
	if(constant('CHECK_USER_NPI')=='YES') {
		$andUserIdQry = " AND user_npi = '".$npi."' AND user_npi != '' AND user_npi != '0' ";			
	}
	include('connect_imwemr.php');
	$userIdQry = "SELECT id FROM users WHERE delete_status='0' AND user_type = '1' ".$andUserIdQry;			
	$userIdRes = imw_query($userIdQry) or die(imw_error());
	if(imw_num_rows($userIdRes)>0) {
		$userIdRow	= imw_fetch_array($userIdRes);
		$userId  	= $userIdRow['id'];
	}
	imw_close($link_imwemr);
	include("common/conDb.php");
	return $userId;
}

//START CODE TO ADD/EDIT ZIPCODE VALUE IN ZIP-CODE TABLE
function addEditZipCodeFun($cityNew,$stateNew,$zipNew) {
	if($zipNew) {
		$objManageData = new manageData;
		unset($arrayZipCodeRecord);
		$arrayZipCodeRecord['city'] 		= $cityNew;
		$arrayZipCodeRecord['state_abb'] 	= $stateNew;
		$arrayZipCodeRecord['zip_code'] 	= $zipNew;
		
		$chkZipCodeExist = $objManageData->getRowRecord('zip_codes', 'zip_code', $zipNew);
		if($chkZipCodeExist) {
			$saveZipCode = $objManageData->updateRecords($arrayZipCodeRecord, 'zip_codes', 'zip_code', $zipNew);
		}else {	
			$saveZipCode = $objManageData->addRecords($arrayZipCodeRecord, 'zip_codes');
		}
	}	
}	
//END CODE TO ADD/EDIT ZIPCODE VALUE IN ZIP-CODE TABLE

function getCoordinatorType($loginUserId) {
	$coordinatorType='';
	if($loginUserId) {
		$coordinatorTypeQry = "SELECT user_type, coordinator_type FROM users WHERE  usersId = '$loginUserId'";
		$coordinatorTypeRes = imw_query($coordinatorTypeQry);
		$coordinatorTypeRows = imw_fetch_array($coordinatorTypeRes);
		$loggedInUserType = $coordinatorTypeRows['user_type'];
		if($loggedInUserType=='Coordinator') {
			$coordinatorType = $coordinatorTypeRows['coordinator_type'];
		}
	}
	return $coordinatorType;
}

function getUserType($loginUserId) {
	$loggedInUserType='';
	if($loginUserId) {
		$userTypeQry = "SELECT user_type, coordinator_type FROM users WHERE  usersId = '$loginUserId'";
		$userTypeRes = imw_query($userTypeQry);
		$userTypeRows = imw_fetch_array($userTypeRes);
		$loggedInUserType = $userTypeRows['user_type'];
		
	}
	return $loggedInUserType;
}


function subStrWord($word) {
	$strLabel='';
	if($word) {
		$word = trim($word);
		$wordArr = explode(' ',$word);
		$wordCount = count($wordArr);
		if($wordCount==1){
			for($i=0;$i<$wordCount;$i++) {
				$strLabel.=substr($wordArr[$i],0,4); //GET 4 CHARACTERS
			}
		}else if($wordCount==2){
			for($i=0;$i<$wordCount;$i++) {
				$strLabel.=substr($wordArr[$i],0,2); //GET FIRST 2 CHARACTERS OF BOTH 2 WORDS
			}
		}else if($wordCount==3){
			for($i=0;$i<$wordCount;$i++) { //GET FIRST CHARACTERS OF FIRST 2 WORDS AND FIRST 2 CHARACTERS OF 3rd WORD
				if($i==0 || $i==1) {
					$strLabel.=substr($wordArr[$i],0,1);
				}else if($i==2) {
					$strLabel.=substr($wordArr[$i],0,2);
				}
			}
		}else if($wordCount>=4){
			for($i=0;$i<4;$i++) {
				$strLabel.=substr($wordArr[$i],0,1); //GET FIRST CHARACTER OF ALL FOUR WORDS
			}
		}
		
		if($strLabel) {
			$strLabel=strtoupper($strLabel);
		}
	}
	return 	$strLabel;
}

function fnLineBrk($str){
		return str_replace(array("\r","\n"),array("\\r","\\n"),$str);
}

function setReSyncroStatus($patient_in_waiting_id,$reSyncroVia) {
	$reSyncro = $reSyncroVia;
	$chkQry = "SELECT reSyncroVia FROM patient_in_waiting_tbl WHERE patient_in_waiting_id='".$patient_in_waiting_id."' AND iAscReSyncroStatus = 'yes' AND reSyncroVia!=''";
	$chkRes =  imw_query($chkQry) or die(imw_error());
	if(imw_num_rows($chkRes)>0){  
		$chkRow = imw_fetch_array($chkRes);
		$reSyncroViaNew =  $chkRow['reSyncroVia'];
		$reSyncro	= $reSyncroViaNew;
		if(stristr($reSyncroViaNew,$reSyncroVia)==false) { $reSyncro	= $reSyncro.",".$reSyncroVia; }
	}
	$reSyncroQry='';
	if($reSyncro || !$reSyncro) {  $reSyncroQry = " , reSyncroVia='".$reSyncro."'";}
	$updateStatusQry = "UPDATE patient_in_waiting_tbl SET iAscReSyncroStatus = 'yes' ".$reSyncroQry." WHERE patient_in_waiting_id='".$patient_in_waiting_id."'";	
	$updateStatusRes =  imw_query($updateStatusQry) or die(imw_error());
	
}

function core_phone_format($phone_number){
	$return = "";
	$refined_phone = preg_replace('/[^0-9]/','',$phone_number);
	$default_format = $GLOBALS['phone_format'];

	switch($default_format){
		case "###-###-####":
			$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $refined_phone);
			break;
		case "(###) ###-####":
			$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $refined_phone);
			break;
		default:
			$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $refined_phone);
			break;
	}
	return $return;
}


//this function logs all the appointment status changes in previous_status table
function logApptChangedStatus($patient_in_waiting_id, $dtNewApptDate, $tmNewApptStartTime, $tmNewApptEndTime, $intNewApptStatusId, $intNewApptProviderId, $intNewApptFacilityId, $strNewApptOpUsername, $strNewApptComments, $intNewApptProcedureId, $blUpdateNew = false){
	include('connect_imwemr.php'); //imwemr connection
	$strQry = "	SELECT 
					id, procedureid , sa_patient_app_status_id, sa_patient_id, sa_app_start_date, sa_app_starttime, sa_app_endtime, 
					sa_comments, sa_facility_id, sa_madeby, sa_doctor_id, sa_comments  
				FROM 
					schedule_appointments 
				WHERE
					iolink_iosync_waiting_id = '".$patient_in_waiting_id."' AND iolink_iosync_waiting_id!='0'";
	$rsData = imw_query($strQry);	
	if(imw_num_rows($rsData)>0) {
		$arrData = imw_fetch_array($rsData);
		
		$intApptId= $arrData['id'];
		$intPatientId = $arrData['sa_patient_id'];				//patient id
		
		$dtOldApptDate = $arrData['sa_app_start_date'];			//old_appt_date
		$tmOldApptStartTime = $arrData['sa_app_starttime'];			//old_appt_start_time
		$tmOldApptEndTime = $arrData['sa_app_endtime'];			//old_appt_end_time
		$intOldApptStatusId = $arrData['sa_patient_app_status_id'];	//old_status
		$intOldApptProviderId = $arrData['sa_doctor_id'];			//old_provider
		$intOldApptFacilityId = $arrData['sa_facility_id'];		//old_facility
		$strOldApptOpUsername = $arrData['sa_madeby'];				//oldMadeBy
		$intOldApptProcedureId = $arrData['procedureid'];				//oldMadeBy
		$strOldApptComments = $arrData['sa_comments'];				//oldMadeBy
		
		if($blUpdateNew == false){
			//$dtNewApptDate = $arrData['sa_app_start_date'];			//New_appt_date
			//$tmNewApptStartTime = $arrData['sa_app_starttime'];			//New_appt_start_time
			//$tmNewApptEndTime = $arrData['sa_app_endtime'];			//New_appt_end_time
			$intNewApptProviderId = $arrData['sa_doctor_id'];			//New_provider
			$intNewApptFacilityId = $arrData['sa_facility_id'];		//New_facility
			$intNewApptProcedureId = $arrData['procedureid'];				//NewMadeBy
		}
		if($tmNewApptStartTime != 0 || $tmNewApptStartTime!='00:00:00') {
			$tmNewApptEndTime = getImedicEndTime($intNewApptProcedureId,$tmNewApptStartTime);
		}else {
			$tmNewApptStartTime	= $arrData['sa_app_start_date'];
			$tmNewApptEndTime = $arrData['sa_app_endtime'];
		}
		
		//making log
		$strInsQry = "INSERT INTO previous_status SET
						sch_id = '".$intApptId."',
						patient_id = '".$intPatientId."',
						status_time = TIME(NOW()),
						status_date = CURDATE(),
						status = '".$intNewApptStatusId."',
						old_date = '".$dtOldApptDate."',
						old_time = '".$tmOldApptStartTime."',
						old_provider = '".$intOldApptProviderId."',
						old_facility = '".$intOldApptFacilityId."',
						statusComments = CONCAT_WS(' ','".addslashes($strNewApptComments)."',statusComments),
						oldStatusComments = '".$strOldApptComments."',
						oldMadeBy = '".$strOldApptOpUsername."',
						statusChangedBy = '".$strNewApptOpUsername."',
						dateTime = '".date("Y-m-d H:i:s")."',
						new_facility = '".$intNewApptFacilityId."',
						new_provider = '".$intNewApptProviderId."',
						old_status = '".$intOldApptStatusId."',
						old_appt_end_time = '".$tmOldApptEndTime."',
						new_appt_date = '".$dtNewApptDate."',
						new_appt_start_time = '".$tmNewApptStartTime."',
						new_appt_end_time = '".$tmNewApptEndTime."',
						old_procedure_id = '".$intOldApptProcedureId."',
						new_procedure_id = '".$intNewApptProcedureId."'";
		
		if($intOldApptStatusId != $intNewApptStatusId){
			imw_query($strInsQry);
		}elseif($intOldApptStatusId == 0 && $intNewApptStatusId == 0){
			imw_query($strInsQry);
		}
	}
	imw_close($link_imwemr); //CLOSE imwemr connection
	include("common/conDb.php");
}


//this function updates appointment details in schedule_appointments tabel
function updateScheduleApptDetails($patient_in_waiting_id, $dtNewApptDate, $tmNewApptStartTime, $tmNewApptEndTime, $intNewApptStatusId, $intNewApptProviderId, $intNewApptFacilityId, $strNewApptOpUsername, $strNewApptComments, $intNewApptProcedureId, $blUpdateNew = false,$pickUpTime,$arrivalTime,$patientSite){
	include('connect_imwemr.php'); //imwemr connection
	//$intStatusOpId = getOpIdFromOpUsername($strNewApptOpUsername);
	$intStatusOpId = '-1';
	
	$strQry = "	SELECT 
					id, procedureid , sa_patient_app_status_id, sa_patient_id, sa_app_start_date, sa_app_starttime, sa_app_endtime,
					sa_comments, sa_facility_id, sa_madeby, sa_doctor_id, sa_comments 
				FROM 
					schedule_appointments 
				WHERE
					iolink_iosync_waiting_id = '".$patient_in_waiting_id."' AND iolink_iosync_waiting_id!='0'";
	$rsData = imw_query($strQry);	
	if(imw_num_rows($rsData)>0) {
		$arrData = imw_fetch_array($rsData);
		$intApptId= $arrData['id'];
		$intPatientId = $arrData['sa_patient_id'];				//patient id
		$prevDosForEMR	= $arrData['sa_app_start_date'];
		
		if($blUpdateNew == false){	
			//$dtNewApptDate = $arrData['sa_app_start_date'];			//New_appt_date
			//$tmNewApptStartTime = $arrData['sa_app_starttime'];			//New_appt_start_time
			//$tmNewApptEndTime = $arrData['sa_app_endtime'];			//New_appt_end_time
			$intNewApptProviderId = $arrData['sa_doctor_id'];			//New_provider
			$intNewApptFacilityId = $arrData['sa_facility_id'];		//New_facility
			$intNewApptProcedureId = $arrData['procedureid'];				//NewMadeBy
		}
	
		if($tmNewApptStartTime != 0 || $tmNewApptStartTime!='00:00:00') {
			$tmNewApptEndTime = getImedicEndTime($intNewApptProcedureId,$tmNewApptStartTime);
		}else {
			$tmNewApptStartTime	= $arrData['sa_app_start_date'];
			$tmNewApptEndTime = $arrData['sa_app_endtime'];
		}
		
		$strUpdQry = "	UPDATE schedule_appointments SET 
							sa_doctor_id = '".$intNewApptProviderId."',
							sa_patient_app_status_id = '".$intNewApptStatusId."',
							sa_comments = CONCAT('".addslashes($strNewApptComments)."', ' ', sa_comments),
							sa_app_time = '".date("Y-m-d H:i:s")."',
							sa_app_starttime = '".$tmNewApptStartTime."',
							sa_app_endtime = '".$tmNewApptEndTime."',
							pick_up_time = '".$pickUpTime."',
							arrival_time = '".$arrivalTime."',
							procedure_site = '".$patientSite."',
							sa_facility_id = '".$intNewApptFacilityId."',
							sa_app_start_date = '".$dtNewApptDate."',
							sa_app_end_date = '".$dtNewApptDate."',
							procedureid = '".$intNewApptProcedureId."',
							sa_madeby = '".$strNewApptOpUsername."',
							status_update_operator_id = '".$intStatusOpId."' 
						WHERE id = '".$intApptId."'";
		imw_query($strUpdQry);
	}
	imw_close($link_imwemr); //CLOSE imwemr connection
	include("common/conDb.php");
	//IN EMR - CANCEL PREVIOUS APPT DATE(IF EXIST) FROM IT IS MOVED
	if(imw_num_rows($rsData)>0 && $intNewApptStatusId=='202') {
		if($prevDosForEMR && $intApptId) {
			$chkEmrApptExistQry = "SELECT stub_id FROM stub_tbl WHERE patient_status='Scheduled' AND dos='".$prevDosForEMR."' AND dos >= '".date('Y-m-d')."' AND appt_id='".$intApptId."'";
			$chkEmrApptExistRes = imw_query($chkEmrApptExistQry) or die(imw_error());
			if(imw_num_rows($chkEmrApptExistRes)>0) {
				$cancelEmrPrevDosQry = "UPDATE stub_tbl SET patient_status='Canceled' WHERE dos='".$prevDosForEMR."' AND dos >= '".date('Y-m-d')."' AND appt_id='".$intApptId."'";
				$cancelEmrPrevDosRes = imw_query($cancelEmrPrevDosQry) or die(imw_error());
			}
		}
	}
	//IN EMR - CANCEL PREVIOUS APPT DATE(IF EXIST) FROM IT IS MOVED
}

function getImedicEndTime($patient_prim_proc_id,$surgery_time) {
	$imedic_endtime='';
	if($patient_prim_proc_id) {
		/*
		$imedicProcedureidQry 		= "SELECT id,proc_time FROM `slot_procedures` WHERE (proc = '".addslashes($patient_prim_proc)."' AND proc!='') OR (acronym = '".addslashes($patient_prim_proc)."' AND acronym!='')  ORDER BY acronym ASC LIMIT 0,1";
		$imedicProcedureidRes 		= imw_query($imedicProcedureidQry) or die($imedicProcedureidQry.imw_error());
		$imedicProcedureidNumRow 	= imw_num_rows($imedicProcedureidRes);
		if($imedicProcedureidNumRow<=0) {
			$imedicProcedureidQry 		= "SELECT id,proc_time FROM `slot_procedures` WHERE (proc = '".addslashes($patient_prim_proc_alias)."' AND proc!='') OR (acronym = '".addslashes($patient_prim_proc_alias)."' AND acronym!='')  ORDER BY acronym ASC LIMIT 0,1";
			$imedicProcedureidRes 		= imw_query($imedicProcedureidQry) or die($imedicProcedureidQry.imw_error());
			$imedicProcedureidNumRow 	= imw_num_rows($imedicProcedureidRes);
		}
		*/
		$imedicProcedureidQry 		= "SELECT id,proc_time FROM `slot_procedures` WHERE id='".$patient_prim_proc_id."'  ORDER BY id ASC LIMIT 0,1";
		$imedicProcedureidRes 		= imw_query($imedicProcedureidQry) or die($imedicProcedureidQry.imw_error());
		$imedicProcedureidNumRow 	= imw_num_rows($imedicProcedureidRes);
		
		if($imedicProcedureidNumRow>0) {
			$imedicProcedureidRow 	= imw_fetch_array($imedicProcedureidRes);
			$imedic_procedureid 	= $imedicProcedureidRow['id'];
			$imedic_proc_time 		= $imedicProcedureidRow['proc_time'];//ID ENTERED TO GET MINUTES FOR DURATION
			
			//START GET DURATION AND END TIME
			$imedicDurationQry 		= "SELECT times FROM `slot_procedures` WHERE id='".$imedic_proc_time."'";
			$imedicDurationRes 		= imw_query($imedicDurationQry) or die($imedicDurationQry.imw_error());
			$imedicDurationNumRow 	= imw_num_rows($imedicDurationRes);
			if($imedicDurationNumRow>0) {
				$imedicDurationRow 	= imw_fetch_array($imedicDurationRes);
				$imedicTimeInMinute = $imedicDurationRow['times'];
				$imedic_duration 	= $imedicTimeInMinute*60;
				
				if($surgery_time != 0 || $surgery_time!='00:00:00') {
					$surgery_timeExplode= explode(":",$surgery_time);
					$imedic_endtime 	= date("H:i:s",mktime($surgery_timeExplode[0],$surgery_timeExplode[1]+$imedicTimeInMinute,$surgery_timeExplode[2],0,0,0));
				}
			}
			//END GET DURATION AND END TIME
		}
	}
	return $imedic_endtime;	
}

//this function return operator id from operator username
function getOpIdFromOpUsername($strNewApptOpUsername){
	$intStatusOpId = 0;
	if($strNewApptOpUsername != ""){
		$strQry = "SELECT id FROM users WHERE username = '".addslashes($strNewApptOpUsername)."'";
		$rsData = imw_query($strQry);	
		$arrData = imw_fetch_array($rsData);
		$intStatusOpId = $arrData['id'];
	}
	return $intStatusOpId;
}


function getQryRes($qry){
	$arrRecords = array();
	$qryId = imw_query($qry) or die(imw_error());
	if(imw_num_rows($qryId)>0){
		if($qryId!=-1) {
		  $qryId = $qryId;
		}
		$fieldCount = imw_num_fields($qryId);
		$k = 0;
		while($record = imw_fetch_array($qryId)) {
			for($i=0;$i<$fieldCount; $i++) {
				$fieldName = imw_field_name($qryId,$i);
				$arrRecords[$k][$fieldName] = $record[$fieldName];
			}
			$k++;
		}
	}
	return $arrRecords; 
}

function smart_wordwrap($string, $width = 75, $break = "<br>") {
    // split on problem words over the line length
    $pattern = sprintf('/([^ ]{%d,})/', $width);
    $output = '';
    $words = preg_split($pattern, $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    foreach ($words as $word) {
        if (false !== strpos($word, ' ')) {
            // normal behaviour, rebuild the string
            $output .= $word;
        } else {
            // work out how many characters would be on the current line
            $wrapped = explode($break, wordwrap($output, $width, $break));
            $count = $width - (strlen(end($wrapped)) % $width);

            // fill the current line and add a break
            $output .= substr($word, 0, $count) . $break;

            // wrap any remaining characters from the problem word
            $output .= wordwrap(substr($word, $count), $width, $break, true);
        }
    }

    // wrap the final output
    return wordwrap($output, $width, $break);
}

?>