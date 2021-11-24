<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(900);
	include_once('connect_imwemr.php'); // imwemr connection
	
	$appt_dos = date("Y-m-d");
	
	//START CODE TO SET/UPDATE sa_patient_id FROM imw TO stub_tbl, patient_data_tbl
	$sqlAppointmentsQry="SELECT * FROM schedule_appointments 
						WHERE sa_app_start_date >= '".$appt_dos."'
						AND sa_patient_app_status_id NOT IN(201,18)
						ORDER BY sa_app_start_date";
						
	
	$sqlAppointmentsRes = imw_query($sqlAppointmentsQry) or die(imw_error());
	$sqlAppointmentsNumRow = imw_num_rows($sqlAppointmentsRes);
	if($sqlAppointmentsNumRow>0) {
		while($sqlAppointmentsRow = imw_fetch_array($sqlAppointmentsRes)) {
			include('connect_imwemr.php'); // imwemr connection
			$appt_id 	       			= $sqlAppointmentsRow['id'];
			$appt_date_of_surgery	   	= $sqlAppointmentsRow['sa_app_start_date'];
			$sa_patient_id	   			= $sqlAppointmentsRow['sa_patient_id']; //PatientId
						
			
			$patientNameQry = "SELECT * FROM patient_data where id='".$sa_patient_id."'";
			$patientNameRes = imw_query($patientNameQry) or die(imw_error());
			$patientNameNumRow = imw_num_rows($patientNameRes);
			
			//START INITIALIZE VARIABLES OF PATIENT PERSONAL INFOpunnam
			$imwPatient_first_name    	= 	"";
			$imwPatient_middle_name   	= 	"";
			$imwPatient_last_name     	= 	"";
			$date_of_birth_patient 			= 	"";
			//END INITIALIZE VARIABLES OF PATIENT PERSONAL INFO
			if($patientNameNumRow>0) {
				$patientNameRow = imw_fetch_array($patientNameRes);
				
				$imwPatient_first_name    	= $patientNameRow['fname']; //Patient First Name
				$imwPatient_middle_name   	= $patientNameRow['mname']; //Patient Middle Name
				$imwPatient_last_name     	= $patientNameRow['lname']; //Patient Last Name
				$date_of_birth_patient 			= $patientNameRow['DOB'];
			}
			
			
			imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
			include("common/conDb.php");  //SURGERYCENTER CONNECTION	
			
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
			$chk_save_appt_id_qry = "SELECT * FROM stub_tbl WHERE 
										patient_first_name = '$imwPatient_first_name'
									 AND patient_last_name = '$imwPatient_last_name'
									 AND patient_dob = '$date_of_birth_patient'
									 AND dos = '$appt_date_of_surgery'
								   ";
								   
			$chk_save_appt_id_res = imw_query($chk_save_appt_id_qry);
			$chk_save_appt_id_NumRow = imw_num_rows($chk_save_appt_id_res);
			//CHECK IF PATIENT ALREADY EXIST (BASED ON NUMBER OF ROWS)
			$pConfId='';
			if($chk_save_appt_id_NumRow > 0) {
				$chk_save_appt_id_Row = imw_fetch_array($chk_save_appt_id_res);
				$pConfId=$chk_save_appt_id_Row['patient_confirmation_id'];
				
				$save_appt_id_qry  		=  "UPDATE stub_tbl SET 
											imwPatientId = '$sa_patient_id'
												WHERE patient_first_name = '$imwPatient_first_name'
												AND patient_last_name = '$imwPatient_last_name'
												AND patient_dob = '$date_of_birth_patient'
												AND dos = '$appt_date_of_surgery'
										   ";
				
				$savePatientDataQry 	= "UPDATE patient_data_tbl SET
											imwPatientId = '$sa_patient_id'
												WHERE patient_fname = '$imwPatient_first_name'
												AND patient_lname = '$imwPatient_last_name'
												AND date_of_birth = '$date_of_birth_patient'
									  	  ";
				$savePatientConfirmQry = "UPDATE patientconfirmation SET
											imwPatientId = '$sa_patient_id'
												WHERE patientConfirmationId = '$pConfId'
												AND patientConfirmationId != ''
												AND patientConfirmationId != '0'
												AND dos = '$appt_date_of_surgery'
									  	 ";
			
				
				if ($imwPatient_first_name != '' && $date_of_birth_patient != '') {
					$save_appt_id_res = imw_query($save_appt_id_qry) or die(imw_error());
					$savePatientDataRes = imw_query($savePatientDataQry) or die(imw_error());
					$savePatientConfirmRes = imw_query($savePatientConfirmQry) or die(imw_error());
				}
			}
			imw_close($link); //CLOSE IMWEMR CONNECTION
		}
	}
	//END CODE TO SET/UPDATE sa_patient_id FROM IMW TO stub_tbl, patient_data_tbl
	
$msg_info[] = "<br><br><b>person_id from IMW updated successfully</b>";

?>
<html>
<head>
<title>Mysql Updates After Launch </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>	
