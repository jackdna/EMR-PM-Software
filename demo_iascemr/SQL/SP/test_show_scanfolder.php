<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");

//GET CONFIRMATION DETAIL
	$getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` ORDER BY patientConfirmationId DESC";
	$getConfirmationDetailRes = imw_query($getConfirmationDetailQry) or die(imw_error()); 
	$getConfirmationDetailNumRow = imw_num_rows($getConfirmationDetailRes);
	if($getConfirmationDetailNumRow>0) {
		$patientName='';
		$patient_dos='';
		while($getConfirmationDetailRow = imw_fetch_array($getConfirmationDetailRes)) { 
			//$patient_confID = '917';
			//$patient_id = '743';
			$patient_confID = $getConfirmationDetailRow['patientConfirmationId'];
			$patient_id = $getConfirmationDetailRow['patientId'];
			$patient_dos = $getConfirmationDetailRow['dos'];
			
			//START GET PATIENT NAME
			$getPatientNameQry = imw_query("SELECT patient_fname,patient_mname,patient_lname
									FROM patient_data_tbl WHERE patient_id = '$patient_id'");
			$getPatientNameNumRow = imw_num_rows($getPatientNameQry);
			if($getPatientNameNumRow>0) {
				$getPatientNameRow = imw_fetch_array($getPatientNameQry);
				$patientFName = $getPatientNameRow['patient_fname'];
				$patientMName = $getPatientNameRow['patient_mname'];
				$patientLName = $getPatientNameRow['patient_lname'];
			}
			if($patientMName) {
				$patientMName = ' '.$patientMName;
			}
			$patientName = $patientLName.', '.$patientFName.$patientMName;
			//END GET PATIENT NAME
			
			//START QRY FOR FOLDER Pt. Info
			$scanFolderPtInfoQry = "select * from scan_documents WHERE confirmation_id='".$patient_confID."' AND document_name='Pt. Info'";
			$scanFolderPtInfoRes = imw_query($scanFolderPtInfoQry) or die(imw_error()); 
			$scanFolderPtInfoNumRow = imw_num_rows($scanFolderPtInfoRes);
			$scanFolderPtInfoIdArr = array();
			if($scanFolderPtInfoNumRow>1) {
				echo '<br><br><b><font color="#FF0000">Pt. Info conf_id</b></font> = '.$patient_confID.' <b>DOS</b> = '.$patient_dos.' <b>Patient Name</b> = '.$patientName;
				
			}
			//END QRY FOR FOLDER Pt. Info
			
			
			//START QRY FOR FOLDER Clinical
			$scanFolderClinicalQry = "select * from scan_documents WHERE confirmation_id='".$patient_confID."' AND document_name='Clinical'";
			$scanFolderClinicalRes = imw_query($scanFolderClinicalQry) or die(imw_error()); 
			$scanFolderClinicalNumRow = imw_num_rows($scanFolderClinicalRes);
			$scanFolderClinicalIdArr = array();
			if($scanFolderClinicalNumRow>1) {
				echo '<br><br><b><font color="#00CC33">Clinical conf_id</b></font> = '.$patient_confID.' <b>DOS</b> = '.$patient_dos.' <b>Patient Name</b> = '.$patientName;
					
			}
			//END QRY FOR FOLDER Clinical
		}	
	}
	
//END GET CONFIRMATION DETAIL


