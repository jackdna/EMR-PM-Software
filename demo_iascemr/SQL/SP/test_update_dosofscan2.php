<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(900);
	include_once("common/conDb.php");  //MYSQL CONNECTION
	
	$getPtIdQry 		= "SELECT patient_id_stub,dos  FROM stub_tbl where dos > '2010-06-15' AND patient_confirmation_id = 0 AND patient_id_stub != 0 order by dos";
	$getPtIdRes 		= imw_query($getPtIdQry) or die(imw_error());
	$getPtIdNumRow 		= imw_num_rows($getPtIdRes);
	if($getPtIdNumRow>0) {
		while($getPtIdRow 	= imw_fetch_array($getPtIdRes)) {
			$confPatientId 	= $getPtIdRow['patient_id_stub'];
			$confDOS 		= $getPtIdRow['dos'];

			$updtscnUpldQry = "UPDATE scan_upload_tbl SET dosOfScan='".$confDOS."' WHERE patient_id='".$confPatientId."' AND confirmation_id='0' AND dosOfScan = '0000-00-00' ";
			//echo $updtscnUpldQry."<br>";
			$updtscnUpldRes = imw_query($updtscnUpldQry) or die(imw_error());

			$getPtId1Qry 		= "SELECT document_id  FROM scan_upload_tbl where  confirmation_id = '0' AND patient_id='".$confPatientId."' " ;
			$getPtId1Res 		= imw_query($getPtId1Qry) or die(imw_error());
			$getPtId1NumRow 		= imw_num_rows($getPtId1Res);
			if($getPtId1NumRow>0) {
				while($getPtId1Row 	= imw_fetch_array($getPtId1Res)) {

					$updtscndocQry = "update scan_documents set   dosOfScan='".$confDOS."' where document_id='".$getPtId1Row['document_id']."' AND dosOfScan='0000-00-00'";
					$updtscndocRes 		= imw_query($updtscndocQry) or die(imw_error());
				}
			}
		
		}
	}
$msg_info[] = "<br><br><b>".$getPtIdNumRow." Records of dosOfScan for confirmed patient in scan_upload_tbl updated Successfully</b> <br>";


	$getPtIdQry 		= "SELECT patientId,patientConfirmationId,dos  FROM patientconfirmation where dos > '2010-06-15' order by dos";
	$getPtIdRes 		= imw_query($getPtIdQry) or die(imw_error());
	$getPtIdNumRow 		= imw_num_rows($getPtIdRes);
	if($getPtIdNumRow>0) {
		while($getPtIdRow 	= imw_fetch_array($getPtIdRes)) {
			$confPatientId 	= $getPtIdRow['patientId'];
			$confId 		= $getPtIdRow['patientConfirmationId'];
			$confDOS 		= $getPtIdRow['dos'];
			$updtscnUpldQry = "UPDATE scan_upload_tbl SET dosOfScan='".$confDOS."', confirmation_id = '".$confId."' WHERE patient_id='".$confPatientId."' AND confirmation_id='0'  ";
			$updtscnUpldRes = imw_query($updtscnUpldQry) or die(imw_error());

			$getPtId1Qry 		= "SELECT document_id  FROM scan_upload_tbl where  confirmation_id = '".$confId."'" ;
			$getPtId1Res 		= imw_query($getPtId1Qry) or die(imw_error());
			$getPtId1NumRow 		= imw_num_rows($getPtId1Res);
			if($getPtId1NumRow>0) {
				while($getPtId1Row 	= imw_fetch_array($getPtId1Res)) {

					$updtscndocQry = "update scan_documents set  confirmation_id = '$confId', dosOfScan='".$confDOS."' where confirmation_id='0' AND document_id='".$getPtId1Row['document_id']."'";
					$updtscndocRes 		= imw_query($updtscndocQry) or die(imw_error());
				}

				
			}


		}
	}

$msg_info[] = "<br><br><b>".$getPtIdNumRow." Records of dosOfScan for confirmed patient in scan_upload_tbl updated Successfully</b>";


$getPtIdQry 		= "SELECT patientId,patientConfirmationId,dos  FROM patientconfirmation where dos > '2010-06-15' order by dos";
	$getPtIdRes 		= imw_query($getPtIdQry) or die(imw_error());
	$getPtIdNumRow 		= imw_num_rows($getPtIdRes);
	if($getPtIdNumRow>0) {
		while($getPtIdRow 	= imw_fetch_array($getPtIdRes)) {
			$confPatientId 	= $getPtIdRow['patientId'];
			$confId 		= $getPtIdRow['patientConfirmationId'];
			$confDOS 		= $getPtIdRow['dos'];
			$updtscnUpldQry = "UPDATE scan_upload_tbl SET dosOfScan='".$confDOS."', confirmation_id = '".$confId."' WHERE patient_id='".$confPatientId."' AND confirmation_id='0'  ";
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