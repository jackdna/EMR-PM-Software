<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	
	include_once('connect_imwemr_remote.php'); // imwemr connection
	include_once("admin/classObjectFunction.php");
	$objManageData = new manageData;
	$patient_dos = $selected_date;
	
	$appt_dos = $patient_dos;
	if($appt_dos=="") {
		$appt_dos = date("Y-m-d");
	}
	$today_imw_date = date("Y-m-d");
	
	$sqlAppointmentsQry="SELECT * FROM schedule_appointments 
						WHERE sa_app_start_date = '".$appt_dos."'
						AND sa_patient_app_status_id NOT IN(201)
						AND sa_facility_id IN(12)
						ORDER BY sa_app_start_date";
						

	$sqlAppointmentsRes = imw_query($sqlAppointmentsQry) or die(imw_error());
	$sqlAppointmentsNumRow = imw_num_rows($sqlAppointmentsRes);
	if($sqlAppointmentsNumRow>0) {
		while($sqlAppointmentsRow = imw_fetch_array($sqlAppointmentsRes)) {
			include('connect_imwemr_remote.php'); // imwemr connection
			$appt_id 	       			= $sqlAppointmentsRow['id'];
			$appt_date_of_surgery	   	= $sqlAppointmentsRow['sa_app_start_date'];
			$sa_doctor_id	   			= $sqlAppointmentsRow['sa_doctor_id']; //Surgeon ID
			$sa_app_starttime  			= $sqlAppointmentsRow['sa_app_starttime']; //Surgery Time
			$sa_patient_id	   			= $sqlAppointmentsRow['sa_patient_id']; //PatientId
			$procedureid	   			= $sqlAppointmentsRow['procedureid']; //ProcedureId
			$comment					= stripslashes($sqlAppointmentsRow['sa_comments']); //Comment
			$patient_status_id			= $sqlAppointmentsRow['sa_patient_app_status_id']; //PATIENT STATUS ID
			
			//SET PATIENT STATUS
			$patient_status='Scheduled';
			if($patient_status_id=='18') {
				$patient_status='Canceled';
			}
			//END SET PATIENT STATUS
			//added by amit on 13-06-09 for synch with imwemr
			$pickup_time = "";
			if(isset($sqlAppointmentsRow['pick_up_time'])){
				$pickup_time = addslashes($sqlAppointmentsRow['pick_up_time']);
			}
			$arrival_time = "";
			if(isset($sqlAppointmentsRow['arrival_time'])){
				$arrival_time = addslashes($sqlAppointmentsRow['arrival_time']);
			}			
			
			$surgeonNameQry = "select * from users where id='".$sa_doctor_id."'";
			$surgeonNameRes = imw_query($surgeonNameQry) or die($surgeonNameQry.imw_error());
			$surgeonNameNumRow = imw_num_rows($surgeonNameRes);
			
			
			//START INITIALIZE VARIABLES OF SURGEON
			$surgeonFirstName    = '';
			$surgeonMiddleName   = '';
			$surgeonLastName     = '';
			
			//END INITIALIZE VARIABLES OF SURGEON
			if($surgeonNameNumRow>0) {
				$surgeonNameRow = imw_fetch_array($surgeonNameRes);
				$surgeonFirstName    = $surgeonNameRow['fname']; //Surgeon First Name
				$surgeonMiddleName   = $surgeonNameRow['mname']; //Surgeon Middle Name
				$surgeonLastName     = $surgeonNameRow['lname']; //Surgeon Last Name
				
			}
			$patientNameQry = "select * from patient_data where id='".$sa_patient_id."'";
			$patientNameRes = imw_query($patientNameQry) or die(imw_error());
			$patientNameNumRow = imw_num_rows($patientNameRes);
			
			//START INITIALIZE VARIABLES OF PATIENT PERSONAL INFO
			$imwPatient_first_name    	= 	"";
			$imwPatient_middle_name   	= 	"";
			$imwPatient_last_name     	= 	"";
			$date_of_birth_patient 			= 	"";
			$sex    	   					= 	"";
			$address_line_1					= 	"";
			$address_line_2 				= 	"";
			$city 							= 	"";
			$state    						= 	"";
			$zip   							= 	"";
			$home_phone    					= 	"";
			$day_phone 						= 	"";
			//END INITIALIZE VARIABLES OF PATIENT PERSONAL INFO
			if($patientNameNumRow>0) {
				$patientNameRow = imw_fetch_array($patientNameRes);
				
				$imwPatient_first_name    	= $patientNameRow['fname']; //Patient First Name
				$imwPatient_middle_name   	= $patientNameRow['mname']; //Patient Middle Name
				$imwPatient_last_name     	= $patientNameRow['lname']; //Patient Last Name
				$date_of_birth_patient 			= $patientNameRow['DOB'];
				$sex    	   					= $patientNameRow['sex'];
				$address_line_1					= $patientNameRow['street'];
				$address_line_2 				= $patientNameRow['street2'];
				$city 							= $patientNameRow['city'];
				$state    						= $patientNameRow['state'];
				$zip   							= $patientNameRow['postal_code'];
				$home_phone    					= $patientNameRow['phone_home'];
				$day_phone 						= $patientNameRow['phone_biz'];
			
			}
			$procedureNameQry = "select * from slot_procedures where id='".$procedureid."'";
			$procedureNameRes = imw_query($procedureNameQry) or die(imw_error());
			$procedureNameNumRow = imw_num_rows($procedureNameRes);
			
			$procedureName   = "";
			$site = "";
			$confSiteNo='';
			if(isset($sqlAppointmentsRow['procedure_site'])){
				$site = strtolower(addslashes($sqlAppointmentsRow['procedure_site']));
				if($site=='bilateral')  { $site='both';}
				
				if($site=='left') 		{ $confSiteNo=1;
				}else if($site=='right'){ $confSiteNo=2;
				}else if($site=='both') { $confSiteNo=3;
				}
			}
			
			$procedureBasedSite='';
			$procedureBasedConfSiteNo='';
			if($procedureNameNumRow>0) {
				$procedureNameRow = imw_fetch_array($procedureNameRes);
				
				$procedureName    = $procedureNameRow['proc']; //Procedure Name
				//$site = "";
				$siteTemp = substr(trim($procedureName),-2,2); //READ LAST TWO CHARACTERS OF PRIMARY PROCEDURE EXCLUDING SPACE
				if($siteTemp=='OS') {
					$procedureBasedSite = 'left';
					$procedureBasedConfSiteNo=1;
					$procedureName = trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}else if($siteTemp=='OD') {
					$procedureBasedSite = 'right';
					$procedureBasedConfSiteNo=2;
					$procedureName = trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}else if($siteTemp=='OU') {
					$procedureBasedSite = 'both';
					$procedureBasedConfSiteNo=3;
					$procedureName = trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}
				$procedureName = addslashes($procedureName);
			
			}
			if($site=='') { //IF SITE IS STILL BLANK THEN SET IT ON PROCEDURE BASED	
				$site 		= $procedureBasedSite;
				$confSiteNo = $procedureBasedConfSiteNo;
			} 
			
			imw_close($link_imwemr_remote); //CLOSE IMWEMR CONNECTION
			include("common/conDb.php");  //SURGERYCENTER CONNECTION	
			
			//START TO FILL DATA INTO STUB TABLE FROM IMW
			
			if($patient_dos >= $today_imw_date) {
				if($sex=='Male') {
					$sex = 'm';
				}else if($sex=='Female') {
					$sex = 'f';
				}
				//SET PATIENT NAME ACCORDING TO REQUIREMENT
				$imwPatient_first_name 		= str_replace("'","",$imwPatient_first_name);
				$imwPatient_middle_name 	= str_replace("'","",$imwPatient_middle_name);
				$imwPatient_last_name 		= str_replace("'","",$imwPatient_last_name);
				
				$imwPatient_first_name 		= addslashes($imwPatient_first_name);
				if($imwPatient_middle_name){
					$imwPatient_middle_name = addslashes($imwPatient_middle_name);
				}
				$imwPatient_last_name 		= addslashes($imwPatient_last_name);
				//END SET PATIENT NAME ACCORDING TO REQUIREMENT 
				
				//CHECK IF PATIENT ALREADY EXIST (BASED ON NUMBER OF ROWS)
							//AND patient_middle_name = '$imwPatient_middle_name'
				
				$chk_save_appt_id_qry 	 = "select * from stub_tbl WHERE 
												imwPatientId = '$sa_patient_id'
											 AND dos = '$appt_date_of_surgery'
											";
				
				/*
				$chk_save_appt_id_qry = "select * from stub_tbl WHERE 
											patient_first_name = '".$imwPatient_first_name."'
										 AND patient_last_name = '".$imwPatient_last_name."'
										 AND patient_dob = '".$date_of_birth_patient."'
										 AND patient_zip = '".$zip."'
										 AND dos = '".$appt_date_of_surgery."'
									   ";
				*/
				$chk_save_appt_id_res 	 = imw_query($chk_save_appt_id_qry);
				$chk_save_appt_id_NumRow = imw_num_rows($chk_save_appt_id_res);
				//CHECK IF PATIENT ALREADY EXIST (BASED ON NUMBER OF ROWS)
				
				
				//SET SURGEON NAME ACCORDING TO REQUIREMENT
				
				$surgeonFirstName 		= str_replace("'","",$surgeonFirstName);
				$surgeonMiddleName 		= str_replace("'","",$surgeonMiddleName);
				$surgeonLastName 		= str_replace("'","",$surgeonLastName);
									
				$surgeonFirstName 		= addslashes($surgeonFirstName);
				$confimSurgeonName='';
				$surgeonMiddleNameConfirm='';
				if($surgeonMiddleName){
					$surgeonMiddleName 	= addslashes($surgeonMiddleName);
					$surgeonMiddleNameConfirm = ' '.$surgeonMiddleName;
				}
				$surgeonLastName 		= addslashes($surgeonLastName);
				
				
				$confimSurgeonName = $surgeonFirstName.$surgeonMiddleNameConfirm.' '.$surgeonLastName;
				
					//GET SURGEON ID
					$confirmSurgeonIdQry = "SELECT usersId FROM users 
											WHERE fname	='$surgeonFirstName'
											AND mname	='$surgeonMiddleName'
											AND lname	='$surgeonLastName'
											AND fname  !=''
										";
					$confirmSurgeonIdRes = imw_query($confirmSurgeonIdQry) or die(imw_error());
					$confirmSurgeonIdNumRow = imw_num_rows($confirmSurgeonIdRes);
					$ConfirmationNxtGnSrgnId='';
					if($confirmSurgeonIdNumRow>0) {
						$confirmSurgeonIdRow = imw_fetch_array($confirmSurgeonIdRes);
						$ConfirmationNxtGnSrgnId = $confirmSurgeonIdRow['usersId'];
					}
					//END GET SURGEON ID
				
				//END SET SURGEON NAME ACCORDING TO REQUIREMENT
				
				$address_line_1 		= addslashes($address_line_1);
				if($address_line_2) {
					$address_line_2		= addslashes($address_line_2);
				}
				$comment 				= addslashes($comment);
				
				//GET PROCEDURE ID 
				$confirmNxtGnProcedureId='';
				$procedureNameConfirmation=$procedureName;
				if($procedureName) {
					if(strpos($procedureName, 'Left Eye') !== false){
						$procedureNameExplode  = explode('Left Eye',$procedureName);
						$procedureNameConfirmation = trim(trim($procedureNameExplode[0]).' '.trim($procedureNameExplode[1]));
						//$site = 'left';
						//$confSiteNo=1;
					}
					if(strpos($procedureName, 'Right Eye') !== false){
						$procedureNameExplode  = explode('Right Eye',$procedureName);
						$procedureNameConfirmation = trim(trim($procedureNameExplode[0]).' '.trim($procedureNameExplode[1]));
						//$site = 'right';
						//$confSiteNo=2;
					}
					if(strpos($procedureName, 'Both Eye') !== false){
						$procedureNameExplode  = explode('Both Eye',$procedureName);
						$procedureNameConfirmation = trim(trim($procedureNameExplode[0]).' '.trim($procedureNameExplode[1]));
						//$site = 'both';
						//$confSiteNo=2;
					}
					
					$confirmNxtGnProcedureIdQry = "select `procedureId`,`name` from procedures where name='".$procedureNameConfirmation."' OR procedureAlias = '".$procedureNameConfirmation."'";
					$confirmNxtGnProcedureIdRes = imw_query($confirmNxtGnProcedureIdQry) or die(imw_error());
					$confirmNxtGnProcedureIdNumRow = imw_num_rows($confirmNxtGnProcedureIdRes);
					if($confirmNxtGnProcedureIdNumRow>0) {
						$confirmNxtGnProcedureIdRow=imw_fetch_array($confirmNxtGnProcedureIdRes);
						$confirmNxtGnProcedureId = $confirmNxtGnProcedureIdRow['procedureId'];
					}
				}		
				//END GET PROCEDURE ID
				
				//START GET PATIENT-ID FROM STUB-TABLE
				$patient_id_stub='';
				if($chk_save_appt_id_NumRow > 0) {
					$chk_save_appt_id_Row = imw_fetch_array($chk_save_appt_id_res);
					$patient_id_stub=$chk_save_appt_id_Row['patient_id_stub'];
				}
				//END GET PATIENT-ID FROM STUB-TABLE
				
				//START GETTING patient_in_waiting_id FROM IOLINK
				$bookedPatientInWatingId='';
				$chkPatientIdDataWaiting='';
				
				if($patient_id_stub) {
					$chkPatientDataTblWaitingQry = "SELECT * FROM patient_data_tbl 
												  WHERE patient_id	='".$patient_id_stub."'";
				}else {
					$chkPatientDataTblWaitingQry = "SELECT * FROM patient_data_tbl 
												  WHERE patient_fname	='".$imwPatient_first_name."'
												  AND patient_lname		='".$imwPatient_last_name."'
												  AND date_of_birth		='".$date_of_birth_patient."'
												  AND zip				='".$zip."'
												  AND patient_id	   !=''
											";
				}
				$chkPatientDataTblWaitingRes 	= imw_query($chkPatientDataTblWaitingQry) or die(imw_error());
				$chkPatientDataTblWaitingNumRow = imw_num_rows($chkPatientDataTblWaitingRes);
				if($chkPatientDataTblWaitingNumRow>0) {
					
					$chkPatientDataTblWaitingRow= imw_fetch_array($chkPatientDataTblWaitingRes);
					$chkPatientIdDataWaiting 	= $chkPatientDataTblWaitingRow['patient_id'];
					
					$chkBookedPatientQry = "SELECT patient_in_waiting_id FROM `patient_in_waiting_tbl`
											WHERE patient_id='".$chkPatientIdDataWaiting."'
											  AND patient_id!=''
											  AND patient_status='Scheduled'
											  AND dos='".$appt_date_of_surgery."'
											";
					$chkBookedPatientRes 	= imw_query($chkBookedPatientQry) or die(imw_error());					
					$chkBookedPatientNumRow = imw_num_rows($chkBookedPatientRes);
					if($chkBookedPatientNumRow>0) {
						$chkBookedPatientRow= imw_fetch_array($chkBookedPatientRes);
						$bookedPatientInWatingId = $chkBookedPatientRow['patient_in_waiting_id'];
					}
				}
				//END GETTING patient_in_waiting_id FROM IOLINK							
				
				
				
				if($chk_save_appt_id_NumRow > 0) {
					
					//START CODE, DO NOT UPDATE RECORD IF ASC-ID IS ALLOCATED TO PATIENT ON SPECIFIC DATE 
					
					$pConfId=$chk_save_appt_id_Row['patient_confirmation_id'];
					
					$chkAscIdExistQry = "SELECT patientConfirmationId FROM `patientconfirmation`
											WHERE `patientConfirmationId` ='$pConfId'
											AND patientConfirmationId != ''
											AND (`ascId` != '' OR `ascId` != '0')
										";		
					
					$chkAscIdExistRes 	= imw_query($chkAscIdExistQry);
					$chkAscIdExistNumRow= imw_num_rows($chkAscIdExistRes);
					//END CODE, DO NOT UPDATE RECORD IF ASC-ID IS ALLOCATED TO PATIENT ON SPECIFIC DATE 
					
					$save_appt_id_qry='';
					$savePatientDataQry='';
					$savePatientConfirmQry='';
					if($chkAscIdExistNumRow) {
						//DO NOT UPDATE RECORD IF ASC-ID IS ALLOCATED TO PATIENT ON SPECIFIC DATE 
					}else {
						$save_appt_id_qry = "update stub_tbl SET 
												appt_id = '$appt_id',
												dos = '$appt_date_of_surgery',
												surgeon_fname = '$surgeonFirstName',
												surgeon_mname = '$surgeonMiddleName',
												surgeon_lname = '$surgeonLastName',
												surgery_time = '$sa_app_starttime',
												pickup_time = '$pickup_time',
												arrival_time = '$arrival_time',
												patient_first_name = '$imwPatient_first_name',
												patient_middle_name = '$imwPatient_middle_name',
												patient_last_name = '$imwPatient_last_name',
												patient_dob = '$date_of_birth_patient',
												patient_sex = '$sex',
												patient_street1 = '$address_line_1',
												patient_street2 = '$address_line_2',
												patient_city = '$city',
												patient_state = '$state',
												patient_zip = '$zip',
												patient_home_phone = '$home_phone',
												patient_work_phone = '$day_phone',
												patient_primary_procedure = '$procedureName',
												patient_status = '$patient_status',
												site = '$site',
												comment = '$comment',
												iolink_patient_in_waiting_id = '$bookedPatientInWatingId',
												imwPatientId = '$sa_patient_id'
												 WHERE imwPatientId = '$sa_patient_id'
												 AND dos = '$appt_date_of_surgery'
												 
												";
												/*
												 WHERE patient_first_name = '".$imwPatient_first_name."'
												 AND patient_last_name = '".$imwPatient_last_name."'
												 AND patient_dob = '".$date_of_birth_patient."'
												 AND patient_zip = '".$zip."'
												 AND dos = '".$appt_date_of_surgery."'
												*/
						$savePatientDataQry = "update patient_data_tbl SET
												patient_fname = '$imwPatient_first_name',
												patient_mname = '$imwPatient_middle_name',
												patient_lname = '$imwPatient_last_name',
												date_of_birth = '$date_of_birth_patient',
												sex = '$sex',
												street1 = '$address_line_1',
												street2 = '$address_line_2',
												city = '$city',
												state = '$state',
												zip = '$zip',
												homePhone = '$home_phone',
												workPhone = '$day_phone',
												imwPatientId = '$sa_patient_id'
												WHERE imwPatientId = '$sa_patient_id'
												  AND imwPatientId != ''
												 
											  ";
												/*
												 WHERE patient_id = '".$chkPatientIdDataWaiting."'
												 AND patient_id != ''
												*/  
						
						$savePatientConfirmQry = "update patientconfirmation SET 
													dos = '$appt_date_of_surgery',
													surgeon_name = '$confimSurgeonName',
													surgeonId = '$ConfirmationNxtGnSrgnId',
													surgery_time = '$sa_app_starttime',
													pickup_time  = '$pickup_time',
													arrival_time = '$arrival_time',
													patient_primary_procedure = '$procedureNameConfirmation',
													patient_primary_procedure_id = '$confirmNxtGnProcedureId',
													site = '$confSiteNo',
													imwPatientId = '$sa_patient_id'
													 WHERE imwPatientId = '$sa_patient_id'
													 AND imwPatientId != ''
													 AND dos = '$appt_date_of_surgery'
												";
													 /*
													 WHERE patientId = '".$chkPatientIdDataWaiting."'
													 AND patientId != ''
													 AND dos = '$appt_date_of_surgery'
													 */
					}
				}else {	
					$save_appt_id_qry = "insert into stub_tbl SET 
											appt_id = '$appt_id',
											dos = '$appt_date_of_surgery',
											surgeon_fname = '$surgeonFirstName',
											surgeon_mname = '$surgeonMiddleName',
											surgeon_lname = '$surgeonLastName',
											surgery_time = '$sa_app_starttime',
											pickup_time = '$pickup_time',
											arrival_time = '$arrival_time',
											patient_first_name = '$imwPatient_first_name',
											patient_middle_name = '$imwPatient_middle_name',
											patient_last_name = '$imwPatient_last_name',
											patient_dob = '$date_of_birth_patient',
											patient_sex = '$sex',
											patient_street1 = '$address_line_1',
											patient_street2 = '$address_line_2',
											patient_city = '$city',
											patient_state = '$state',
											patient_zip = '$zip',
											patient_home_phone = '$home_phone',
											patient_work_phone = '$day_phone',
											patient_primary_procedure = '$procedureName',
											patient_status = '$patient_status',
											site = '$site',
											comment = '$comment',
											iolink_patient_in_waiting_id = '$bookedPatientInWatingId',
											imwPatientId = '$sa_patient_id'
											";
				}
				
				if($save_appt_id_qry) {	
					if ($imwPatient_first_name != '' && $date_of_birth_patient != '') {
						$save_appt_id_res = imw_query($save_appt_id_qry) or die(imw_error());
						
						if($savePatientDataQry) {
							$savePatientDataRes = imw_query($savePatientDataQry) or die(imw_error());
						}
						if($savePatientConfirmQry) {
							$savePatientConfirmRes = imw_query($savePatientConfirmQry) or die(imw_error());
						}
						
					}
				}	
			}	
			//END TO FILL DATA INTO STUB TABLE FROM IMW
			imw_close($link); //CLOSE IMWEMR CONNECTION
		}
	}	

	include("common/conDb.php");  //SURGERYCENTER CONNECTION	
	//SET CURRENT DATE STATUS = YES
	if($patient_dos==date("Y-m-d")) {
		$chk_CurDateTableQry = "select * from cur_date_tbl_status where sqlServerDate ='$patient_dos'";
		$chk_CurDateTableRes = imw_query($chk_CurDateTableQry);
		$chk_CurDateTableNumRow = imw_num_rows($chk_CurDateTableRes);
		if($chk_CurDateTableNumRow>0) {
			$insCurDateTableQry = "update cur_date_tbl_status set sqlDateStatus = 'yes' where sqlServerDate ='$patient_dos'";
		}else {
			$insCurDateTableQry = "insert into cur_date_tbl_status set sqlServerDate ='$patient_dos', sqlDateStatus = 'yes'";
		}
		$insCurDateTableRes = imw_query($insCurDateTableQry) or die(imw_error());
	}
	//END SET CURRENT DATE STATUS = YES 
	
?>