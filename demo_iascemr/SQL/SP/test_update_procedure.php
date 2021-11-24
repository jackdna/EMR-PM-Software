<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>

<?php
include_once("common/conDb.php");

$tdate = date('Y-m-d');
$getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE dos>='".$tdate."' AND ascId='0' ORDER BY dos DESC";
$getConfirmationDetailRes = imw_query($getConfirmationDetailQry) or die(imw_error()); 
$getConfirmationDetailNumRow = imw_num_rows($getConfirmationDetailRes);
if($getConfirmationDetailNumRow>0) {
	while($getConfirmationDetailRow = imw_fetch_array($getConfirmationDetailRes)) { 
		$patientConfirmationId =  $getConfirmationDetailRow['patientConfirmationId'];
		$patient_primary_procedure =  stripslashes($getConfirmationDetailRow['patient_primary_procedure']);
		
		if($patient_primary_procedure) {
			$primaryProcedureQry ="SELECT * FROM procedures WHERE name='".addslashes($patient_primary_procedure)."' OR procedureAlias = '".addslashes($patient_primary_procedure)."'";
			$primaryProcedureRes = imw_query($primaryProcedureQry) or die(imw_error());
			if(imw_num_rows($primaryProcedureRes)>0) {
				$primaryProcedureRow=imw_fetch_array($primaryProcedureRes);
				$primaryProcedure_id = $primaryProcedureRow['procedureId'];
				$patient_primary_procedure_name = stripslashes($primaryProcedureRow['name']);
			
				$updatePrimaryProcedureQry = "UPDATE patientconfirmation SET patient_primary_procedure='".addslashes($patient_primary_procedure_name)."' WHERE patientConfirmationId='".$patientConfirmationId."'";
				$updatePrimaryProcedureRes = imw_query($updatePrimaryProcedureQry) or die(imw_error());
				
			}
		}	
		
	}
}
$msg_info[] = "<br><br><b>Procedures Updated Successfully</b>";

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
