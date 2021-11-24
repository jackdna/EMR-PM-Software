<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(900);
	include_once("common/conDb.php");  //MYSQL CONNECTION
	
	//START CODE TO UPDATE PATIENT-ID IN STUB TABLE
	$stubTblQry 	= "SELECT * FROM stub_tbl WHERE (patient_id_stub='0' OR patient_id_stub='') ORDER BY stub_id ASC";
	$stubTblRes 	= imw_query($stubTblQry);
	$stubTblNumRow 	= imw_num_rows($stubTblRes);
	if($stubTblNumRow>0) {
		while($stubTblRow = imw_fetch_array($stubTblRes)) {
			$stubTblStubId 			= $stubTblRow['stub_id'];
			$stubTblPatientFirstName= $stubTblRow['patient_first_name'];
			$stubTblPatientLastName = $stubTblRow['patient_last_name'];
			$stubTblPatientDob 		= $stubTblRow['patient_dob'];
			$stubTblDos 			= $stubTblRow['dos'];
			$stubTblConfirmationId 	= $stubTblRow['patient_confirmation_id'];
			$stubTblPatientIdStub 	= $stubTblRow['patient_id_stub'];
			
			$confPatientId='';
			if($stubTblConfirmationId) {
				$getPtIdQry 		= "SELECT patientId  FROM patientconfirmation WHERE patientConfirmationId='".$stubTblConfirmationId."'";
				$getPtIdRes 		= imw_query($getPtIdQry) or die(imw_error());
				$getPtIdNumRow 		= imw_num_rows($getPtIdRes);
				if($getPtIdNumRow>0) {
					$getPtIdRow 	= imw_fetch_array($getPtIdRes);
					$confPatientId 	= $getPtIdRow['patientId'];
				}
			}else {
				$getPtDataIdQry 	= "SELECT patient_id FROM patient_data_tbl 
										WHERE patient_fname = '$stubTblPatientFirstName'
										AND patient_lname 	= '$stubTblPatientLastName'
										AND date_of_birth 	= '$stubTblPatientDob'
										ORDER BY patient_id DESC
										";
				$getPtDataIdRes 	= imw_query($getPtDataIdQry) or die(imw_error());
				$getPtDataIdNumRow 	= imw_num_rows($getPtDataIdRes);
				if($getPtDataIdNumRow>0) {
					$getPtDataIdRow = imw_fetch_array($getPtDataIdRes);
					$confPatientId 	= $getPtDataIdRow['patient_id'];
				}
			}
			if($confPatientId) {
				$updateStubTblPatientIdQry = "UPDATE stub_tbl SET patient_id_stub='".$confPatientId."' WHERE stub_id='".$stubTblStubId."'";
				$updateStubTblPatientIdRes = imw_query($updateStubTblPatientIdQry) or die(imw_error());
			}
		}
	}
	//END CODE TO UPDATE PATIENT-ID IN STUB TABLE

$msg_info[] = "<br><br><b>PATIENT-ID in stub table updated Successfully</b>";

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