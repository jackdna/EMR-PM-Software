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

	$getStbIdQry 				= "SELECT patient_id_stub  FROM stub_tbl where dos > '2010-06-15' order by dos";
	$getStbIdRes 				= imw_query($getStbIdQry) or die(imw_error());
	$getStbIdNumRow 			= imw_num_rows($getStbIdRes);
	if($getStbIdNumRow>0) {
		while($getStbIdRow 		= imw_fetch_array($getStbIdRes)) {
			$patient_id_stub	= $getStbIdRow['patient_id_stub'];
			if($patient_id_stub) {
				//START QRY FOR FOLDER Pt. Info
				$scanFolderPtInfoQry = "select document_id from scan_documents WHERE confirmation_id='0' AND patient_id='".$patient_id_stub."' AND document_name='Pt. Info'";
				$scanFolderPtInfoRes = imw_query($scanFolderPtInfoQry) or die(imw_error()); 
				$scanFolderPtInfoNumRow = imw_num_rows($scanFolderPtInfoRes);
				if($scanFolderPtInfoNumRow>1) {
					while($scanFolderPtInfoRow=imw_fetch_array($scanFolderPtInfoRes)) {
						$scanFolderPtInfoId = $scanFolderPtInfoRow['document_id'];
						
						$scanDocumentPtInfoQry = "select scan_upload_id from scan_upload_tbl WHERE document_id ='".$scanFolderPtInfoId."'";
						$scanDocumentPtInfoRes = imw_query($scanDocumentPtInfoQry) or die(imw_error()); 
						$scanDocumentPtInfoNumRow = imw_num_rows($scanDocumentPtInfoRes);
						$scanDocumentPtInfoId='';
						if($scanDocumentPtInfoNumRow<=0) {
							echo '<br>'.$delScanFolderPtInfoQry = "DELETE FROM scan_documents WHERE document_id='".$scanFolderPtInfoId."'";
							$delScanFolderPtInfoRes = imw_query($delScanFolderPtInfoQry) or die(imw_error()); 
						}
					}	
				}
				$scanFolderPtInfoChkQry = "select document_id from scan_documents WHERE confirmation_id='0' AND patient_id='".$patient_id_stub."' AND document_name='Pt. Info'";
				$scanFolderPtInfoChkRes = imw_query($scanFolderPtInfoChkQry) or die(imw_error()); 
				$scanFolderPtInfoChkNumRow = imw_num_rows($scanFolderPtInfoChkRes);
				if($scanFolderPtInfoChkNumRow<=0) {
					$insertscanFolderPtInfoQry = "insert into `scan_documents` set 
												document_name = 'Pt. Info',
												patient_id = '".$patient_id_stub."'
												";
					$insertscanFolderPtInfoRes = imw_query($insertscanFolderPtInfoQry) or die(imw_error()); 							
					
				}
				//END QRY FOR FOLDER Pt. Info
				
				//START QRY FOR FOLDER Clinical
				$scanFolderClinicalQry = "select document_id from scan_documents WHERE confirmation_id='0' AND patient_id='".$patient_id_stub."' AND document_name='Clinical'";
				$scanFolderClinicalRes = imw_query($scanFolderClinicalQry) or die(imw_error()); 
				$scanFolderClinicalNumRow = imw_num_rows($scanFolderClinicalRes);
				if($scanFolderClinicalNumRow>1) {
					while($scanFolderClinicalRow=imw_fetch_array($scanFolderClinicalRes)) {
						$scanFolderClinicalId = $scanFolderClinicalRow['document_id'];
						
						$scanDocumentClinicalQry = "select scan_upload_id from scan_upload_tbl WHERE document_id ='".$scanFolderClinicalId."'";
						$scanDocumentClinicalRes = imw_query($scanDocumentClinicalQry) or die(imw_error()); 
						$scanDocumentClinicalNumRow = imw_num_rows($scanDocumentClinicalRes);
						$scanDocumentClinicalId='';
						if($scanDocumentClinicalNumRow<=0) {
							echo '<br>'.$delScanFolderClinicalQry = "DELETE FROM scan_documents WHERE document_id='".$scanFolderClinicalId."'";
							$delScanFolderClinicalRes = imw_query($delScanFolderClinicalQry) or die(imw_error()); 
						}
					}	
				}
				$scanFolderClinicalChkQry = "select document_id from scan_documents WHERE confirmation_id='0' AND patient_id='".$patient_id_stub."' AND document_name='Clinical'";
				$scanFolderClinicalChkRes = imw_query($scanFolderClinicalChkQry) or die(imw_error()); 
				$scanFolderClinicalChkNumRow = imw_num_rows($scanFolderClinicalChkRes);
				if($scanFolderClinicalChkNumRow<=0) {
					$insertscanFolderClinicalQry = "insert into `scan_documents` set 
												document_name = 'Clinical',
												patient_id = '".$patient_id_stub."',
												";
					$insertscanFolderClinicalRes = imw_query($insertscanFolderClinicalQry) or die(imw_error()); 							
					
				}
				//END QRY FOR FOLDER Clinical			
			}
		}
	}	



$msg_info[] = "<br><br>Previous record of Scan folder <b>WITHOUT CONFIRMATION-ID</b> updated/corrected successfully";

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
