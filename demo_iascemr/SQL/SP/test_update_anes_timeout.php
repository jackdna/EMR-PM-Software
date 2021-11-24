<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("common/conDb.php");

$opRoomRecordQry = "SELECT * FROM `operatingroomrecords` WHERE verifiedbyAnesthesiologist='Yes'";
$opRoomRecordRes = imw_query($opRoomRecordQry) or die(imw_error()); 
$opRoomRecordNumRow = imw_num_rows($opRoomRecordRes);

if($opRoomRecordNumRow>0) {
	while($opRoomRecordRow = imw_fetch_array($opRoomRecordRes)) {
		$opRoomConfimId = $opRoomRecordRow['confirmation_id'];
		
		$getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE patientConfirmationId='".$opRoomConfimId."' AND dos<'2009-10-05'";
		$getConfirmationDetailRes = imw_query($getConfirmationDetailQry) or die(imw_error()); 
		$getConfirmationDetailNumRow = imw_num_rows($getConfirmationDetailRes);
		
		$patientConfirmationAnesId='';
		if($getConfirmationDetailNumRow>0) {
			$getConfirmationDetailRow = imw_fetch_array($getConfirmationDetailRes);
			$patientConfirmationAnesId =  $getConfirmationDetailRow['anesthesiologist_id'];
		}
		
		$userName = "";
		if($patientConfirmationAnesId) {
			$viewUserNameQry = "select * from `users` where  usersId = '".$patientConfirmationAnesId."'";
			$viewUserNameRes = imw_query($viewUserNameQry) or die(imw_error()); 
			$viewUserNameNumRow = imw_num_rows($viewUserNameRes);
			if($viewUserNameNumRow>0) {
				$viewUserNameRow = imw_fetch_array($viewUserNameRes); 
				if($viewUserNameRow["lname"]) {
					$userName = $viewUserNameRow["lname"].", ".$viewUserNameRow["fname"]." ".$viewUserNameRow["mname"];
				}
			}
			
		}
		
		if($userName) {
			$updateOpRoomRecordQry = "UPDATE `operatingroomrecords` SET 									
										verifiedbyAnesthesiologistName = '".addslashes($userName)."'
									 WHERE confirmation_id='".$opRoomConfimId."'";
			
			$updateOpRoomRecordRes = imw_query($updateOpRoomRecordQry) or die(imw_error());						 
			
		}
		
	}
}



$msg_info[] = "<br><br><b>Time Out Anesthesia Updated Succesfully</b>";
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

