<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("common/conDb.php");  //MYSQL SURGERYCENTER CONNECTION

$getStubTblQry = "SELECT * FROM stub_tbl ORDER BY stub_id DESC";
$getStubTblRes = imw_query($getStubTblQry) or die(imw_error());
$getStubTblNumRow = imw_num_rows($getStubTblRes);
if($getStubTblNumRow>0) {
	while($getStubTblRow 	= imw_fetch_array($getStubTblRes)) {
		include("common/conDb.php");
		$stub_id 			= $getStubTblRow['stub_id'];
		$patient_first_name = $getStubTblRow['patient_first_name'];
		$patient_last_name 	= $getStubTblRow['patient_last_name'];
		$patient_dob 		= $getStubTblRow['patient_dob'];
		$patient_zip 		= $getStubTblRow['patient_zip'];
		$imwPatientId 	= $getStubTblRow['imwPatientId'];
				
		imw_close($link); //CLOSE SURGERYCENTER CONNECTION
		if($imwPatientId) {
			include("connect_imwemr.php");  // imwemr connection	
			$getImwPtDataQry = "SELECT pid FROM patient_data 
								WHERE fname='".addslashes($patient_first_name)."'
								AND lname='".addslashes($patient_last_name)."'
								AND DOB='".addslashes($patient_dob)."'
								AND postal_code='".addslashes($patient_zip)."'
								";
			$getImwPtDataRes = imw_query($getImwPtDataQry) or die(imw_error());
			$getImwPtDataNumRow = imw_num_rows($getImwPtDataRes);
			if($getImwPtDataNumRow>0) {
				$getImwPtDataRow = imw_fetch_array($getImwPtDataRes);
				$pid = $getImwPtDataRow['pid'];
				
				imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
				include("common/conDb.php");  //SURGERYCENTER CONNECTION	
				//if($imwPatientId){
					echo '<br>'.$updtStubTblQry = "UPDATE stub_tbl SET imwPatientId='".$pid."' WHERE imwPatientId='".addslashes($imwPatientId)."'";
					$updtStubTblRes = imw_query($updtStubTblQry) or die(imw_error());
				
					$updtStubTblQry = "UPDATE patient_data_tbl SET imwPatientId='".$pid."' WHERE imwPatientId='".addslashes($imwPatientId)."'";
					$updtStubTblRes = imw_query($updtStubTblQry) or die(imw_error());
		
					$updtStubTblQry = "UPDATE patientconfirmation SET imwPatientId='".$pid."' WHERE imwPatientId='".addslashes($imwPatientId)."'";
					$updtStubTblRes = imw_query($updtStubTblQry) or die(imw_error());
				//}
				imw_close($link); //CLOSE SURGERYCENTER CONNECTION
			}	
		}	
	}
	$msg_info='Update imwPatientId of duplicate patient run successfully';
}else {
	$msg_info='No Record Found';
}
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