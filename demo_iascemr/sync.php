<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	include_once("common/conDb.php");  //MYSQL CONNECTION
	  
	include('connect_sqlserver.php'); //SQL SERVER CONNECTION -- //GET SERVER ID(PRACTICE ID) AND OTHER CONFIGRATION ALSO
	$patient_dos = $selected_date;
	if($patient_dos) {
		list($appt_year,$appt_month,$appt_day) = explode("-",$patient_dos);
		$appt_dos = $appt_year.$appt_month.$appt_day;
	}
	if($appt_dos=="") {
		$appt_dos = date("Ymd");
	}
	$today_imw_date = date("Y-m-d");
	
	$sql_appointments="SELECT * FROM appointments 
						WHERE appt_date = '$appt_dos' 
						AND practice_id='$practice_id_config' 
						AND cancel_ind='$cancel_ind' 
						AND delete_ind='$delete_ind'
						order by modify_timestamp";
	//$sql_appointments="SELECT * FROM appointments where person_id = 'E1DFF64D-7821-42BC-B2BB-D4D91257A1E9'";
	$rs_appointments = $db->Execute($sql_appointments);
	if($rs_appointments) {
		foreach($rs_appointments as $k => $row_appointments) {
			$appt_id 	       = $row_appointments[1];
			$person_id         = $row_appointments[3];  //Get Patient's Personal Information through person_id
			$event_id  		   = $row_appointments[2];  //Get Site(Left, Right, Both) on the base of event_id
			$refer_provider_id = $row_appointments[32]; //Surgeon ID
			$practice_id   	   = $row_appointments[0];  //Server ID
			
			$appt_date     	   = $row_appointments[4];
			$begintime         = $row_appointments[5];  //Surgery Time
			$user_defined2     = $row_appointments[23]; //Arival Time
			$user_defined1     = $row_appointments[22]; //PickUp Time
			$comment     	   = $row_appointments[25]; //details
			
			$sql_provider="SELECT * FROM provider_mstr where provider_id = '$refer_provider_id'";
			$rs_provider = $db->Execute($sql_provider);
			
			//START INITIALIZE VARIABLES OF SURGEON
				$provider_first_name    = ''; 
				$provider_middle_name   = ''; 
				$provider_last_name     = ''; 
			//END INITIALIZE VARIABLES OF SURGEON
			if($rs_provider) {
				foreach($rs_provider as $j => $row_provider) {
					$provider_first_name    = $row_provider[3]; //Surgeon First Name
					$provider_middle_name   = $row_provider[4]; //Surgeon Middle Name
					$provider_last_name     = $row_provider[2]; //Surgeon Last Name
				
				}
			}
			$sql_person="SELECT * FROM person where person_id = '$person_id'";
			$rs_person = $db->Execute($sql_person);
			
			//START INITIALIZE VARIABLES OF PATIENT PERSONAL INFO
				$imwPatient_first_name    	= ''; 
				$imwPatient_middle_name   	= ''; 
				$imwPatient_last_name     	= ''; 
				$date_of_birth 	= '';
				$sex    	   	= '';
				$address_line_1 = '';
				$address_line_2 = '';
				$city 			= '';
				$state    		= '';
				$zip   			= '';
				$home_phone     = '';
				$day_phone 		= '';
			//END INITIALIZE VARIABLES OF PATIENT PERSONAL INFO

			if($rs_person) {
				foreach($rs_person as $i => $row_person) {
					$imwPatient_first_name    	= $row_person[7]; //Patient First Name
					$imwPatient_middle_name   	= $row_person[8]; //Patient Middle Name
					$imwPatient_last_name     	= $row_person[6]; //Patient Last Name
					$date_of_birth 	= $row_person[38];
					$sex    	   	= $row_person[39];
					$address_line_1 = $row_person[13];
					$address_line_2 = $row_person[14];
					$city 			= $row_person[15];
					$state    		= $row_person[16];
					$zip   			= $row_person[17];
					$home_phone     = $row_person[22];
					$day_phone 		= $row_person[33];
				
				}
			}
			$sql_event="SELECT * FROM events where event_id = '$event_id'";
			$rs_event = $db->Execute($sql_event);
			
			//START INTIALIZE VARIABLES
				$event='';
				$siteTemp='';
				$site='';
			//END INTIALIZE VARIABLES
			if($rs_event) {
				foreach($rs_event as $h => $row_event) {
					$event    = $row_event[2];
					$siteTemp = substr(trim($event),-2,2); //READ LAST TWO CHARACTERS OF PRIMARY PROCEDURE EXCLUDING SPACE
					if($siteTemp=='OS') {
						$site = 'left';
						$event = trim(str_replace($siteTemp,'',$event)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
					}else if($siteTemp=='OD') {
						$site = 'right';
						$event = trim(str_replace($siteTemp,'',$event)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
					}else if($siteTemp=='OU') {
						$site = 'both';
						$event = trim(str_replace($siteTemp,'',$event)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
					}
					$event = addslashes($event);
				
				}
			}	
			
			//START TO FILL DATA INTO STUB TABLE FROM IMW
			$appt_date_of_surgery='';
			$date_of_birth_patient='';
			$surgery_time='';
			if($patient_dos >= $today_imw_date) {
				
				if($date_of_birth) {	
					$date_of_birth_patient = (substr($date_of_birth,0,4)."-".substr($date_of_birth,4,2)."-".substr($date_of_birth,6,2));
				}
				if($appt_date) {
					$appt_date_of_surgery = (substr($appt_date,0,4)."-".substr($appt_date,4,2)."-".substr($appt_date,6,2));
				}
				if($begintime) {
					$surgery_time = (substr($begintime,0,2).":".substr($begintime,2,2).":00");
				}
				$arrival_time = "";
				if($user_defined2) {
					$arrival_time = $user_defined2;
				}
				$pickup_time="";
				if($user_defined1) {
					$pickup_time = $user_defined1;
				}
				$sex = strtolower($sex);
				
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
				
				/*
				$chk_save_appt_id_qry = "select * from stub_tbl WHERE 
											 imwPatientId = '$person_id'
										 AND dos = '$appt_date_of_surgery'
									   ";
				*/
				$chk_save_appt_id_qry = "select * from stub_tbl WHERE 
											patient_first_name = '".$imwPatient_first_name."'
										 AND patient_last_name = '".$imwPatient_last_name."'
										 AND patient_dob = '".$date_of_birth_patient."'
										 AND patient_zip = '".$zip."'
										 AND dos = '".$appt_date_of_surgery."'
									   ";
				
				
				$chk_save_appt_id_res = imw_query($chk_save_appt_id_qry);
				$chk_save_appt_id_NumRow = imw_num_rows($chk_save_appt_id_res);
				//CHECK IF PATIENT ALREADY EXIST (BASED ON NUMBER OF ROWS)
				
				
				//SET SURGEON NAME ACCORDING TO REQUIREMENT
				$provider_first_name 		= str_replace("'","",$provider_first_name);
				$provider_middle_name 		= str_replace("'","",$provider_middle_name);
				$provider_last_name 		= str_replace("'","",$provider_last_name);
									
				$provider_first_name 		= addslashes($provider_first_name);
				if($provider_middle_name){
					$provider_middle_name 	= addslashes($provider_middle_name);
				}
				$provider_last_name 		= addslashes($provider_last_name);
				//END SET SURGEON NAME ACCORDING TO REQUIREMENT
			
				
				$address_line_1 			= addslashes($address_line_1);
				if($address_line_2) {
					$address_line_2			= addslashes($address_line_2);
				}
				$comment 					= addslashes($comment);
				
				
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
													  WHERE patient_fname='".$imwPatient_first_name."'
													  AND patient_lname='".$imwPatient_last_name."'
													  AND date_of_birth='".$date_of_birth_patient."'
													  AND zip='".$zip."'
													  AND patient_id!=''
													";
				}
				$chkPatientDataTblWaitingRes = imw_query($chkPatientDataTblWaitingQry) or die(imw_error());
				$chkPatientDataTblWaitingNumRow = imw_num_rows($chkPatientDataTblWaitingRes);
				if($chkPatientDataTblWaitingNumRow>0) {
					$chkPatientDataTblWaitingRow = imw_fetch_array($chkPatientDataTblWaitingRes);
					$chkPatientIdDataWaiting = $chkPatientDataTblWaitingRow['patient_id'];
					
					$chkBookedPatientQry = "SELECT patient_in_waiting_id FROM `patient_in_waiting_tbl`
											WHERE patient_id='".$chkPatientIdDataWaiting."'
											  AND patient_id!=''
											  AND patient_status='Scheduled'
											  AND dos='".$appt_date_of_surgery."'
											";
					$chkBookedPatientRes = 	imw_query($chkBookedPatientQry) or die(imw_error());					
					$chkBookedPatientNumRow = imw_num_rows($chkBookedPatientRes);
					if($chkBookedPatientNumRow>0) {
						$chkBookedPatientRow = imw_fetch_array($chkBookedPatientRes);
						$bookedPatientInWatingId = $chkBookedPatientRow['patient_in_waiting_id'];
					}
				}
				//END GETTING patient_in_waiting_id FROM IOLINK
				
				if($chk_save_appt_id_NumRow > 0) {
					$save_appt_id_qry = "update stub_tbl SET 
											appt_id = '$appt_id',
											dos = '$appt_date_of_surgery',
											surgeon_fname = '$provider_first_name',
											surgeon_mname = '$provider_middle_name',
											surgeon_lname = '$provider_last_name',
											surgery_time = '$surgery_time',
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
											patient_primary_procedure = '$event',
											site = '$site',
											comment = '$comment',
											iolink_patient_in_waiting_id = '$bookedPatientInWatingId',
											imwPatientId = '$person_id'
											 WHERE patient_first_name = '".$imwPatient_first_name."'
											 AND patient_last_name = '".$imwPatient_last_name."'
											 AND patient_dob = '".$date_of_birth_patient."'
											 AND patient_zip = '".$zip."'
											 AND dos = '".$appt_date_of_surgery."'
											";
											 /*
											 WHERE imwPatientId = '$person_id'
											 AND dos = '$appt_date_of_surgery'
											*/
				}else {	
					$save_appt_id_qry = "insert into stub_tbl SET 
											appt_id = '$appt_id',
											dos = '$appt_date_of_surgery',
											surgeon_fname = '$provider_first_name',
											surgeon_mname = '$provider_middle_name',
											surgeon_lname = '$provider_last_name',
											surgery_time = '$surgery_time',
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
											patient_primary_procedure = '$event',
											patient_status = 'Scheduled',
											site = '$site',
											comment = '$comment',
											iolink_patient_in_waiting_id = '$bookedPatientInWatingId',
											imwPatientId = '$person_id'
											";
				}
				if ($imwPatient_first_name != '' && $date_of_birth_patient != '') {
					$save_appt_id_res = imw_query($save_appt_id_qry) or die(imw_error());
				}
			
			}	
			//END TO FILL DATA INTO STUB TABLE FROM IMW
		}
	}	
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
	
	$db->close();
	
?>