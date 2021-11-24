<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;


//GET CONFIRMATION DETAIL
	$getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` where dos > '2010-06-15'  ORDER BY patientConfirmationId";
	$getConfirmationDetailRes = imw_query($getConfirmationDetailQry) or die(imw_error()); 
	$getConfirmationDetailNumRow = imw_num_rows($getConfirmationDetailRes);
	if($getConfirmationDetailNumRow>0) {
		while($getConfirmationDetailRow = imw_fetch_array($getConfirmationDetailRes)) { 
			//$patient_confID = '917';
			//$patient_id = '743'; 
			$patient_confID = $getConfirmationDetailRow['patientConfirmationId'];
			$patient_id = $getConfirmationDetailRow['patientId'];
			
			//START QRY FOR FOLDER Pt. Info
			$scanFolderPtInfoQry = "select * from scan_documents WHERE confirmation_id='".$patient_confID."' AND document_name='Pt. Info'";
			$scanFolderPtInfoRes = imw_query($scanFolderPtInfoQry) or die(imw_error()); 
			$scanFolderPtInfoNumRow = imw_num_rows($scanFolderPtInfoRes);
			$scanFolderPtInfoIdArr = array();
			if($scanFolderPtInfoNumRow>1) {
				while($scanFolderPtInfoRow=imw_fetch_array($scanFolderPtInfoRes)) {
					$scanFolderPtInfoId = $scanFolderPtInfoRow['document_id'];
					
					$scanDocumentPtInfoQry = "select * from scan_upload_tbl WHERE document_id ='".$scanFolderPtInfoId."'";
					$scanDocumentPtInfoRes = imw_query($scanDocumentPtInfoQry) or die(imw_error()); 
					$scanDocumentPtInfoNumRow = imw_num_rows($scanDocumentPtInfoRes);
					$scanDocumentPtInfoId='';
					if($scanDocumentPtInfoNumRow<=0) {
						$delScanFolderPtInfoQry = "DELETE FROM scan_documents WHERE document_id='".$scanFolderPtInfoId."'";
						$delScanFolderPtInfoRes = imw_query($delScanFolderPtInfoQry) or die(imw_error()); 
					}
				}	
			}
			$scanFolderPtInfoChkQry = "select * from scan_documents WHERE confirmation_id='".$patient_confID."' AND document_name='Pt. Info'";
			$scanFolderPtInfoChkRes = imw_query($scanFolderPtInfoChkQry) or die(imw_error()); 
			$scanFolderPtInfoChkNumRow = imw_num_rows($scanFolderPtInfoChkRes);
			if($scanFolderPtInfoChkNumRow<=0) {
				$insertscanFolderPtInfoQry = "insert into `scan_documents` set 
											document_name = 'Pt. Info',
											patient_id = '".$patient_id."',
											confirmation_id='".$patient_confID."'
											";
				$insertscanFolderPtInfoRes = imw_query($insertscanFolderPtInfoQry) or die(imw_error()); 							
				
			}
			//END QRY FOR FOLDER Pt. Info
			
			
			//START QRY FOR FOLDER Clinical
			$scanFolderClinicalQry = "select * from scan_documents WHERE confirmation_id='".$patient_confID."' AND document_name='Clinical'";
			$scanFolderClinicalRes = imw_query($scanFolderClinicalQry) or die(imw_error()); 
			$scanFolderClinicalNumRow = imw_num_rows($scanFolderClinicalRes);
			$scanFolderClinicalIdArr = array();
			if($scanFolderClinicalNumRow>1) {
				while($scanFolderClinicalRow=imw_fetch_array($scanFolderClinicalRes)) {
					$scanFolderClinicalId = $scanFolderClinicalRow['document_id'];
					
					$scanDocumentClinicalQry = "select * from scan_upload_tbl WHERE document_id ='".$scanFolderClinicalId."'";
					$scanDocumentClinicalRes = imw_query($scanDocumentClinicalQry) or die(imw_error()); 
					$scanDocumentClinicalNumRow = imw_num_rows($scanDocumentClinicalRes);
					$scanDocumentClinicalId='';
					if($scanDocumentClinicalNumRow<=0) {
						$delScanFolderClinicalQry = "DELETE FROM scan_documents WHERE document_id='".$scanFolderClinicalId."'";
						$delScanFolderClinicalRes = imw_query($delScanFolderClinicalQry) or die(imw_error()); 
					}
				}	
			}
			$scanFolderClinicalChkQry = "select * from scan_documents WHERE confirmation_id='".$patient_confID."' AND document_name='Clinical'";
			$scanFolderClinicalChkRes = imw_query($scanFolderClinicalChkQry) or die(imw_error()); 
			$scanFolderClinicalChkNumRow = imw_num_rows($scanFolderClinicalChkRes);
			if($scanFolderClinicalChkNumRow<=0) {
				$insertscanFolderClinicalQry = "insert into `scan_documents` set 
											document_name = 'Clinical',
											patient_id = '".$patient_id."',
											confirmation_id='".$patient_confID."'
											";
				$insertscanFolderClinicalRes = imw_query($insertscanFolderClinicalQry) or die(imw_error()); 							
				
			}
			//END QRY FOR FOLDER Clinical
		}	
	}
	
//END GET CONFIRMATION DETAIL 


$msg_info[] = "<br><br><b>Previous record of Scan folder updated/corrected successfully</b>";

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
