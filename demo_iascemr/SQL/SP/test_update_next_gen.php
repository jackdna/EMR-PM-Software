<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(900);
	include_once("common/conDb.php");  //MYSQL CONNECTION
	  
	include('connect_sqlserver.php'); //SQL SERVER CONNECTION -- //GET SERVER ID(PRACTICE ID) AND OTHER CONFIGRATION ALSO
	$appt_dos = date("Ymd");
	
	//START CODE TO SET/UPDATE person_id FROM IMW TO stub_tbl
	$sql_appointments="SELECT * FROM appointments 
						WHERE appt_date >= '$appt_dos' 
						AND practice_id='$practice_id_config' 
						AND cancel_ind='$cancel_ind' 
						AND delete_ind='$delete_ind'
						order by modify_timestamp";
	
	$rs_appointments = $db->Execute($sql_appointments);
	if($rs_appointments) {
		foreach($rs_appointments as $k => $row_appointments) {
			$appt_id 	       = $row_appointments[1];
			$person_id         = $row_appointments[3];  //Get Patient's Personal Information through person_id
			$practice_id   	   = $row_appointments[0];  //Server ID
			$appt_date     	   = $row_appointments[4];
		
			$sql_person="SELECT * FROM person where person_id = '$person_id'";
			$rs_person = $db->Execute($sql_person);
			
			//START INITIALIZE VARIABLES OF PATIENT PERSONAL INFO
				$imwPatient_first_name    	= ''; 
				$imwPatient_middle_name   	= ''; 
				$imwPatient_last_name     	= ''; 
				$date_of_birth 	= '';
			//END INITIALIZE VARIABLES OF PATIENT PERSONAL INFO

			if($rs_person) {
				foreach($rs_person as $i => $row_person) {
					$imwPatient_first_name    	= $row_person[7]; //Patient First Name
					$imwPatient_middle_name   	= $row_person[8]; //Patient Middle Name
					$imwPatient_last_name     	= $row_person[6]; //Patient Last Name
					$date_of_birth 					= $row_person[38];
				}
			}
			
			$date_of_birth_patient='';
			$appt_date_of_surgery='';
			if($date_of_birth) {	
				$date_of_birth_patient = (substr($date_of_birth,0,4)."-".substr($date_of_birth,4,2)."-".substr($date_of_birth,6,2));
			}
			if($appt_date) {
				$appt_date_of_surgery = (substr($appt_date,0,4)."-".substr($appt_date,4,2)."-".substr($appt_date,6,2));
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
			
			$chk_save_person_id_qry = "select * from stub_tbl WHERE 
										 patient_first_name = '$imwPatient_first_name'
									 AND patient_last_name = '$imwPatient_last_name'
									 AND patient_dob = '$date_of_birth_patient'
									 AND dos = '$appt_date_of_surgery'
			   ";
			$chk_save_person_id_res = imw_query($chk_save_person_id_qry);
			$chk_save_person_id_NumRow = imw_num_rows($chk_save_person_id_res);
			if($chk_save_person_id_NumRow > 0) {
				$save_person_id_qry = "update stub_tbl SET imwPatientId = '$person_id'
									 WHERE patient_first_name = '$imwPatient_first_name'
									   AND patient_last_name = '$imwPatient_last_name'
									   AND patient_dob = '$date_of_birth_patient'
									   AND dos = '$appt_date_of_surgery'";			
				
				if ($imwPatient_first_name != '' && $date_of_birth_patient != '') {
					$save_person_id_res = imw_query($save_person_id_qry) or die(imw_error());
				}
			}
		}
	}
	//END CODE TO SET/UPDATE person_id FROM imw TO stub_tbl		

$msg_info[] = "<br><br><b>person_id updated successfully</b>";

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
