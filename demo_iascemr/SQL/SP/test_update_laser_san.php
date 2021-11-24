<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>

<?php
include_once("common/conDb.php");

$tdate = date('Y-m-d');
$getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE dos<='".$tdate."' ORDER BY dos DESC";
$getConfirmationDetailRes = imw_query($getConfirmationDetailQry) or die(imw_error()); 
$getConfirmationDetailNumRow = imw_num_rows($getConfirmationDetailRes);
if($getConfirmationDetailNumRow>0) {
	while($getConfirmationDetailRow = imw_fetch_array($getConfirmationDetailRes)) { 
		$patientConfirmationId =  $getConfirmationDetailRow['patientConfirmationId'];
		$patient_primary_procedure_id =  $getConfirmationDetailRow['patient_primary_procedure_id'];
		
		
		$primaryProcedureQry ="SELECT * FROM procedures WHERE procedureId='".$patient_primary_procedure_id."'";
		$primaryProcedureRes = imw_query($primaryProcedureQry) or die(imw_error());
		$procedureCatId='';
		if(imw_num_rows($primaryProcedureRes)>0) {
			$primaryProcedureRow=imw_fetch_array($primaryProcedureRes);
			$procedureCatId = $primaryProcedureRow['catId'];
		}	
		//UPDATE LASER CHART NOTE IF ITS PROCEDURE IS NOT LASER 
		if($procedureCatId!='2') {
			$updateSignQry = "UPDATE `laser_procedure_patient_table` SET 
										signSurgeon1Id = '',
										signSurgeon1FirstName = '', 
										signSurgeon1MiddleName = '',
										signSurgeon1LastName = '', 
										signSurgeon1Status = '',
										signSurgeon1DateTime = '',
										signNurseId = '',
										signNurseFirstName = '', 
										signNurseMiddleName = '',
										signNurseLastName = '', 
										signNurseStatus = '',
										signNurseDateTime = '',
										form_status = ''
										WHERE confirmation_id='".$patientConfirmationId."'";
										
			$updateSignRes = imw_query($updateSignQry) or die(imw_error());
			
		}
		//UPDATE LASER CHART NOTE IF ITS PROCEDURE IS NOT LASER 
	}
}
$msg_info[] = "<br><br><b>Signatures and flags on Laser ChartNote removed/updated successfully</b>";

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
