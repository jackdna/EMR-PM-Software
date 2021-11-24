<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(600);
	include_once("../../common/conDb.php");  //MYSQL CONNECTION
	
	//UPDATE FOR CEC SERVER
	$getUserStatusQry = "SELECT usersId FROM users WHERE usersId = '4' AND deleteStatus='Yes' AND fname='William' AND lname='Wiley'";
	$getUserStatusRes = imw_query($getUserStatusQry) or die(imw_error());
	
	if(imw_num_rows($getUserStatusRes)>0) {
		$patientConfirmationIdArr = array('0');
		$getPtConfUserIdQry = "SELECT patientConfirmationId FROM patientconfirmation WHERE surgeonId = '4'";
		$getPtConfUserIdRes = imw_query($getPtConfUserIdQry) or die(imw_error());
		if(imw_num_rows($getPtConfUserIdRes)>0) {
			while($getPtConfUserIdRow = imw_fetch_array($getPtConfUserIdRes)) {
				$patientConfirmationIdArr[] = 	$getPtConfUserIdRow["patientConfirmationId"];	
			}
			$ptConfImplode = implode(",",$patientConfirmationIdArr);

			$updtPreOpPhyQry = "UPDATE preopphysicianorders SET prefilMedicationStatus='' WHERE patient_confirmation_id != '0' AND patient_confirmation_id IN(".$ptConfImplode.")";
			$updtPreOpPhyRes = imw_query($updtPreOpPhyQry) or die(imw_error());
			
			echo $updtPreOpPhyQry = "UPDATE patientconfirmation SET surgeonId='36' WHERE patientConfirmationId != '0' AND patientConfirmationId IN(".$ptConfImplode.")";
			$updtPreOpPhyRes = imw_query($updtPreOpPhyQry) or die(imw_error());
			
			$msg_info[] = "<br><br><b>All Prefill Medication Status are updated successfully</b>";
		}else {
			$msg_info[] = "<br><br><b>No record found to update Prefill Medication Status</b>";	
		}
	}else {
		$msg_info[] = "<br><br><b>This update is for CEC server only to set Prefill Medication Status and this is safe update</b>";	
	}
	//UPDATE FOR CEC SERVER



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