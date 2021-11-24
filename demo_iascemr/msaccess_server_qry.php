<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(500);
	//include_once("common/conDb.php");  //MYSQL CONNECTION
	  
	include('connect_msaccess_server.php'); //MICROSOFT ACCESS SERVER CONNECTION 
?>
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="50"><b>id</b></td>
		<td>&nbsp;</td>
		<td><b>appointment_type_name</b></td>
		<td>&nbsp;</td>
		<td><b>appointment_type_mne</b></td>
		<td>&nbsp;</td>
		<td><b>appointment_type_length</b></td>
		<td>&nbsp;</td>
		<td><b>appointment_type_backcolor</b></td>
		<td>&nbsp;</td>
		<td><b>appointment_type_forecolor</b></td>
	</tr>
<?php
	$sql_appointments_type="SELECT appointment_type.* FROM appointment_type";
				
	$rs_appointments = $db->Execute($sql_appointments_type) or die(odbc_errormsg());
	if($rs_appointments) {
		foreach($rs_appointments as $k => $row_appointments) {
			$id 	       				= $row_appointments[0];		
			$appointment_type_name 		= $row_appointments[1]; 	
			$appointment_type_mne 		= $row_appointments[2]; 	
			$appointment_type_length 	= $row_appointments[3];  	
			$appointment_type_backcolor	= $row_appointments[4];  	
			$appointment_type_forecolor	= $row_appointments[5];  	
	?>

			<tr>
				<td><?php echo $id;?></td>
				<td>&nbsp;</td>
				<td><?php echo $appointment_type_name;?></td>
				<td>&nbsp;</td>
				<td><?php echo $appointment_type_mne;?></td>
				<td>&nbsp;</td>
				<td><?php echo $appointment_type_length;?></td>
				<td>&nbsp;</td>
				<td><?php echo $appointment_type_backcolor;?></td>
				<td>&nbsp;</td>
				<td><?php echo $appointment_type_forecolor;?></td>
			</tr>
	<?php		
			
		}
	}else {
	?>
			<tr>
				<td><b>No Procedure Found</b></td>
			</tr>	
	<?php	
	}
	?>
</table>	

<?php
/*
//START CODE TO GET PROCEDURE OF SELECTED APPOINTMENT DATE
		$patient_dos = '2010-02-05';
		if($patient_dos) {
			$appt_dos = date("m/d/Y",strtotime($patient_dos));
		}
		list($appt_dosMonth,$appt_dosDay,$appt_dosYear) = explode('/',$appt_dos);
		$appt_dosNextDay = date("m/d/Y",mktime(0,0,0,$appt_dosMonth,$appt_dosDay+1,$appt_dosYear));

		$sql_appointmentsNew="
					SELECT appointments.* FROM appointments 
					WHERE appointments.appointment_date_time > #$appt_dos#  
					AND appointments.appointment_date_time < #$appt_dosNextDay# 
					
					ORDER BY DateValue([appointment_date_time]), TimeValue([appointment_date_time]);
					";
					
		$rs_appointmentsNew = $db->Execute($sql_appointmentsNew) or die(odbc_errormsg());
		if($rs_appointmentsNew) {
			foreach($rs_appointmentsNew as $k => $row_appointmentsNew) {
				$appt_id 	       		= $row_appointmentsNew[0];		//appt_id
				$appointment_provider 	= $row_appointmentsNew[1]; 	//SURGEON ID
				$appt_date 	       		= $row_appointmentsNew[4]; 	//DOS
				$appointment_patient 	= $row_appointmentsNew[3];  	//PATIENT ID
				$appointment_site		= $row_appointmentsNew[6];  	//SITE ID	
				$appointment_activity	= $row_appointmentsNew[7];  	//Reference id for PRIMARY PROCEDURE
				
				echo '<br>Procedure No. = '.stripslashes($appointment_activity);
			
			}
		
		}	
*/		
//START CODE TO GET PROCEDURE OF SELECTED APPOINTMENT DATE		
?>			
