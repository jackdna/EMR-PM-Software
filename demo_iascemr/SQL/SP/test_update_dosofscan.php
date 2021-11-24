<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(900);
	include_once("common/conDb.php");  //MYSQL CONNECTION
	
	//START SET PATIENT-ID IN stub_tbl
	$getStbIdQry 				= "SELECT *  FROM stub_tbl where dos > '2010-06-15' AND patient_id_stub ='0' order by dos";
	$getStbIdRes 				= imw_query($getStbIdQry) or die(imw_error());
	$getStbIdNumRow 			= imw_num_rows($getStbIdRes);
	if($getStbIdNumRow>0) {
		while($getStbIdRow 		= imw_fetch_array($getStbIdRes)) {
			$stub_id						= $getStbIdRow['stub_id'];
			$stub_tbl_patient_first_name	= $getStbIdRow['patient_first_name'];
			$stub_tbl_patient_last_name 	= $getStbIdRow['patient_last_name'];
			$stub_tbl_patient_dob 			= $getStbIdRow['patient_dob'];
			$patient_zip 					= $getStbIdRow['patient_zip'];
	
			$patientMatchSearchStr = "SELECT patient_id FROM patient_data_tbl 
									WHERE patient_fname = '".addslashes($stub_tbl_patient_first_name)."'
									AND patient_lname 	= '".addslashes($stub_tbl_patient_last_name)."'
									AND zip 			= '".addslashes($patient_zip)."'
									AND date_of_birth 	= '".$stub_tbl_patient_dob."'
									ORDER BY patient_id DESC
									";
			$patientPatientSearchDataId='';
			$patientMatchSearchRes = imw_query($patientMatchSearchStr);
			$patientMatchSearchNumRows = imw_num_rows($patientMatchSearchRes);
			if($patientMatchSearchNumRows>0){
				$patientDataSearchRow = imw_fetch_array($patientMatchSearchRes);
				$patientPatientSearchDataId = $patientDataSearchRow['patient_id'];
			}
			if($patientPatientSearchDataId) {
				echo '<br>'.$updtStbQry = "UPDATE stub_tbl SET patient_id_stub='".$patientPatientSearchDataId."' WHERE stub_id='".$stub_id."' AND patient_id_stub='0'";
				$updtStbRes = imw_query($updtStbQry) or die(imw_error());
			}	
			
		}
	}		
	//END SET PATIENT-ID IN stub_tbl	
	
	$getPtIdQry 		= "SELECT patientId,patientConfirmationId,dos  FROM patientconfirmation where dos > '2010-06-15' order by dos";
	$getPtIdRes 		= imw_query($getPtIdQry) or die(imw_error());
	$getPtIdNumRow 		= imw_num_rows($getPtIdRes);
	if($getPtIdNumRow>0) {
		while($getPtIdRow 	= imw_fetch_array($getPtIdRes)) {
			$confPatientId 	= $getPtIdRow['patientId'];
			$confId 		= $getPtIdRow['patientConfirmationId'];
			$confDOS 		= $getPtIdRow['dos'];
			echo '<br>'.$updtscnUpldQry = "UPDATE scan_upload_tbl SET dosOfScan='".$confDOS."' WHERE patient_id='".$confPatientId."' AND confirmation_id='".$confId."' AND dosOfScan = '0000-00-00' ";
			$updtscnUpldRes = imw_query($updtscnUpldQry) or die(imw_error());
		}
	}
$msg_info[] = "<br><br><b>".$getPtIdNumRow." Records of dosOfScan for confirmed patient in scan_upload_tbl updated Successfully</b>";
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