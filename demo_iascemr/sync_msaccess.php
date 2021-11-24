<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(500);
	include_once("common/conDb.php");  //MYSQL CONNECTION
	  
	include('connect_msaccess_server.php'); //MICROSOFT ACCESS SERVER CONNECTION 
	$patient_dos = $selected_date;
	//$patient_dos = '2009-05-20';
	if($patient_dos) {
		//$appt_dos = date("d/m/Y",strtotime($patient_dos));
		$appt_dos = date("m/d/Y",strtotime($patient_dos));
	}
	if($appt_dos=="") {
		//$appt_dos = date("d/m/Y");
		$appt_dos = date("m/d/Y");
	}
	
	//GET NEXT DAY OF APPT.DOS
	
	//list($appt_dosDay,$appt_dosMonth,$appt_dosYear) = explode('/',$appt_dos);
	//$appt_dosNextDay = date("d/m/Y",mktime(0,0,0,$appt_dosMonth,$appt_dosDay+1,$appt_dosYear));
	list($appt_dosMonth,$appt_dosDay,$appt_dosYear) = explode('/',$appt_dos);
	$appt_dosNextDay = date("m/d/Y",mktime(0,0,0,$appt_dosMonth,$appt_dosDay+1,$appt_dosYear));
	
	//END GET NEXT DAY OF APPT.DOS
	
	$today_imw_date = date("Y-m-d");

		$sql_appointments="
					SELECT appointments.* FROM appointments 
					WHERE appointments.appointment_date_time > #$appt_dos#  
					AND appointments.appointment_date_time < #$appt_dosNextDay# 
					AND appointments.appointment_activity_type in(1,6,7,9,25,27,31,40,41,45,46,50,51,52,53,54,55,58,59,60,61,62,63,64,65,67,72,75,76,77,78,80,81,82,83,84,85,86,87,88,89,91)
					AND appointments.appointment_status=0 
					ORDER BY DateValue([appointment_date_time]), TimeValue([appointment_date_time]);
					";
					
		$rs_appointments = $db->Execute($sql_appointments) or die(odbc_errormsg());
		if($rs_appointments) {
			foreach($rs_appointments as $k => $row_appointments) {
				$appt_id 	       		= $row_appointments[0];		//appt_id
				$appointment_provider 	= $row_appointments[1]; 	//SURGEON ID
				$appt_date 	       		= $row_appointments[4]; 	//DOS
				$appointment_patient 	= $row_appointments[3];  	//PATIENT ID
				$appointment_site		= $row_appointments[6];  	//SITE ID	
				$appointment_activity	= $row_appointments[7];  	//Reference id for PRIMARY PROCEDURE
			

				//GET SURGEON DETAIL
					$sql_providerId="SELECT providers.providers_doctor,providers.providers_first_name,providers.providers_last_name FROM providers where id = $appointment_provider";
					$rs_providerId = $db->Execute($sql_providerId);
					if($rs_providerId) {
						foreach($rs_providerId as $j => $row_providerId) {
							$providers_doctor    = $row_providerId[0]; //PROVIDER-DOCTOR ID
						
							$doctor_first_name    = $row_providerId[1]; //Surgeon First Name
							$doctor_last_name     = $row_providerId[2]; //Surgeon Last Name
							//$doctor_middle_name   = $row_provider[2]; //Surgeon Middle Name
						
						}
					}
					/*
					$sql_provider="SELECT doctor.doctor_first_name,doctor.doctor_last_name FROM doctor where id = $providers_doctor";
					$rs_provider = $db->Execute($sql_provider);
					if($rs_provider) {
						foreach($rs_provider as $k => $row_provider) {
							$doctor_first_name    = $row_provider[0]; //Surgeon First Name
							$doctor_last_name     = $row_provider[1]; //Surgeon Last Name
							//$doctor_middle_name   = $row_provider[2]; //Surgeon Middle Name
						
						}
					}*/
				//END GET SURGEON DETAIL
				
				//GET PATIENT DETAIL
					$sql_patientDetail="SELECT patients.* FROM patients where ID = $appointment_patient";
					$rs_patientDetail = $db->Execute($sql_patientDetail);
					if(count($rs_patientDetail)>0) {
						foreach($rs_patientDetail as $i => $row_patientDetail) {
							$imwPatient_first_name  	= $row_patientDetail[2]; 	//Patient First Name
							$imwPatient_middle_name   	= $row_patientDetail[3]; 	//Patient Middle Name
							$imwPatient_last_name     	= $row_patientDetail[1]; 	//Patient Last Name
							$patient_birthdate 				= $row_patientDetail[9]; 	//Patient DOB
							$patient_sex    	   			= $row_patientDetail[12]; 	//Patient Sex
							$patient_addr_1 				= $row_patientDetail[4];	//Patient Address1
							$patient_addr_2 				= $row_patientDetail[5];	//Patient Address2
							$patient_phone     				= $row_patientDetail[8];	//Patient Home Phone
							$patient_phone_2 				= $row_patientDetail[47];	//Patient Work Phone
							
							$patient_csz 					= $row_patientDetail[6];	//Reference id for city,state,zip
						}
					}
					
					$sql_csz="SELECT zipcodes.zip_city, zipcodes.zip_state, zipcodes.zip_zip_code FROM zipcodes where ID = $patient_csz";
					$rs_csz = $db->Execute($sql_csz);
					if($rs_csz) {
						foreach($rs_csz as $m => $row_csz) {
							$zip_city 		= addslashes($row_csz[0]);	//City
							$zip_state    	= $row_csz[1];	//State
							$zip_zip_code   = $row_csz[2];	//Zip
						
						}
					}
				//END GET PATIENT DETAIL
					
				//GET PRIMARY PROCEDURE
					$sql_procedure="SELECT appointment_type.appointment_type_name FROM appointment_type where id = $appointment_activity";
					$rs_procedure = $db->Execute($sql_procedure);
					if($rs_procedure) {
						foreach($rs_procedure as $m => $row_procedure) {
							$appointment_type_name 	= addslashes($row_procedure[0]);	//PRIMARY PROCEDURE
						
						}
					}
				//END GET PRIMARY PROCEDURE
			
				//GET SITE OF PATIENT (LEFT, RIGHT, BOTH)
					/*
					$sql_site="SELECT sites.site_name FROM sites where ID = $appointment_site";
					$rs_site = $db->Execute($sql_site);
					if($rs_site) {
						foreach($rs_site as $m => $row_site) {
							echo "<br>".$site_name 		= $row_site[0];	//PATIENT SITE
						
						}
					}
					*/
				//END SITE OF PATIENT
				
				//START TO FILL DATA INTO STUB TABLE FROM IMW
				if($patient_dos >= $today_imw_date) {
					if($patient_birthdate) {	
						$date_of_birth_patient 	= date("Y-m-d",strtotime($patient_birthdate));
					}
					if($appt_date) {
						$appt_date_of_surgery 	= date("Y-m-d",strtotime($appt_date)); //DOS
						$surgery_time 			= date("H:i:s",strtotime($appt_date)); //SURGERY TIME
					}
					/*
					$arrival_time = "";
					if($user_defined2) {
						$arrival_time = $user_defined2;
					}
					$pickup_time="";
					if($user_defined1) {
						$pickup_time = $user_defined1;
					}
					*/
					$patient_sex = strtolower($patient_sex);
					
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
					$chk_save_appt_id_qry = "select * from stub_tbl WHERE 
												patient_first_name = '$imwPatient_first_name'
											 AND patient_last_name = '$imwPatient_last_name'
											 AND patient_dob = '$date_of_birth_patient'
											 AND dos = '$appt_date_of_surgery'
										   ";
					$chk_save_appt_id_res = imw_query($chk_save_appt_id_qry);
					$chk_save_appt_id_NumRow = imw_num_rows($chk_save_appt_id_res);
					//CHECK IF PATIENT ALREADY EXIST (BASED ON NUMBER OF ROWS)
					
					
					//SET SURGEON NAME ACCORDING TO REQUIREMENT
					$doctor_first_name 			= str_replace("'","",$doctor_first_name);
					$doctor_middle_name 		= str_replace("'","",$doctor_middle_name);
					$doctor_last_name 			= str_replace("'","",$doctor_last_name);
										
					$doctor_first_name 			= addslashes($doctor_first_name);
					if($doctor_middle_name){
						$doctor_middle_name 	= addslashes($doctor_middle_name);
					}
					$doctor_last_name 			= addslashes($doctor_last_name);
					//END SET SURGEON NAME ACCORDING TO REQUIREMENT
					
					$patient_addr_1 			= addslashes($patient_addr_1);
					if($patient_addr_2) {
						$patient_addr_2			= addslashes($patient_addr_2);
					}
					if($comment) {
						$comment 				= addslashes($comment);
					}
					if($chk_save_appt_id_NumRow > 0) {
						$save_appt_id_qry = "update stub_tbl SET 
												appt_id = '$appt_id',
												dos = '$appt_date_of_surgery',
												surgeon_fname = '$doctor_first_name',
												surgeon_mname = '$doctor_middle_name',
												surgeon_lname = '$doctor_last_name',
												surgery_time = '$surgery_time',
												pickup_time = '$pickup_time',
												arrival_time = '$arrival_time',
												patient_first_name = '$imwPatient_first_name',
												patient_middle_name = '$imwPatient_middle_name',
												patient_last_name = '$imwPatient_last_name',
												patient_dob = '$date_of_birth_patient',
												patient_sex = '$patient_sex',
												patient_street1 = '$patient_addr_1',
												patient_street2 = '$patient_addr_2',
												patient_city = '$zip_city',
												patient_state = '$zip_state',
												patient_zip = '$zip_zip_code',
												patient_home_phone = '$patient_phone',
												patient_work_phone = '$patient_phone_2',
												patient_primary_procedure = '$appointment_type_name',
												site = '$site_name',
												comment = '$comment'
												 WHERE patient_first_name = '$imwPatient_first_name'
												 AND patient_last_name = '$imwPatient_last_name'
												 AND patient_dob = '$date_of_birth_patient'
												 AND dos = '$appt_date_of_surgery'
												
												";
						
					}else {	
						$save_appt_id_qry = "insert into stub_tbl SET 
												appt_id = '$appt_id',
												dos = '$appt_date_of_surgery',
												surgeon_fname = '$doctor_first_name',
												surgeon_mname = '$doctor_middle_name',
												surgeon_lname = '$doctor_last_name',
												surgery_time = '$surgery_time',
												pickup_time = '$pickup_time',
												arrival_time = '$arrival_time',
												patient_first_name = '$imwPatient_first_name',
												patient_middle_name = '$imwPatient_middle_name',
												patient_last_name = '$imwPatient_last_name',
												patient_dob = '$date_of_birth_patient',
												patient_sex = '$patient_sex',
												patient_street1 = '$patient_addr_1',
												patient_street2 = '$patient_addr_2',
												patient_city = '$zip_city',
												patient_state = '$zip_state',
												patient_zip = '$zip_zip_code',
												patient_home_phone = '$patient_phone',
												patient_work_phone = '$patient_phone_2',
												patient_primary_procedure = '$appointment_type_name',
												patient_status = 'Scheduled',
												site = '$site_name',
												comment = '$comment'
												";
					}
					$save_appt_id_res = imw_query($save_appt_id_qry) or die(imw_error());
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