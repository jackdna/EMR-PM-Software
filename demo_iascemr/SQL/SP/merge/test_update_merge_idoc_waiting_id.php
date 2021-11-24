<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
	set_time_limit(0);
	include_once("../../../common/conDb.php");  //MYSQL CONNECTION
	include_once("test_update_merge_db_detail.php");  //DB Details
	
	if($IDOC_DB)
	{
		imw_query("CREATE TABLE ".$IDOC_DB.".schedule_appointments_bak_".date("d_m_Y")." AS (SELECT * FROM ".$IDOC_DB.".schedule_appointments)") or $msg_info[] = imw_error();
		
		$RECORD_UPDATE=0;
		$scemrQuery = imw_query("SELECT patient_id_stub, iolink_patient_in_waiting_id, appt_id,imwPatientId FROM stub_tbl ORDER BY stub_id ASC ") or $msg_info[] = $scemrQuery.imw_error();
		if(imw_error())$msg_info[] = imw_error();
		while($scemrData = imw_fetch_object($scemrQuery))
		{
			$idoc_apptId		=	trim($scemrData->appt_id);
			$scemr_patientId	=	trim($scemrData->patient_id_stub);
			$scemr_waitingId	=	trim($scemrData->iolink_patient_in_waiting_id);
			$idoc_patient_id	=	trim($scemrData->imwPatientId);
			if(trim($scemr_waitingId)){
				$qry 	=	"UPDATE ".$IDOC_DB.".schedule_appointments SET iolink_iosync_waiting_id =".$scemr_waitingId.", iolinkPatientWtId = ".$scemr_waitingId.",  iolinkPatientId = ".$scemr_patientId." WHERE id ='".$idoc_apptId."' ";
				imw_query($qry) or $msg_info[] = $qry.imw_error();
				$RECORD_UPDATE++;
			}
			if($scemr_patientId && $idoc_patient_id) {
				$idoc_iolink_patient_id_new = trim('drOffice'.$idoc_patient_id.'-'.$scemr_patientId);
				$qry_patient 	=	"UPDATE ".$IDOC_DB.".patient_data SET idoc_iolink_patient_id ='".$idoc_iolink_patient_id_new."' WHERE pid ='".$idoc_patient_id."' ";
				imw_query($qry_patient) or $msg_info[] = $qry_patient.imw_error();
			}
		}
		$msg_info[] = "<br><br><b> Update Completed</b><br/>$RECORD_UPDATE Records Updated.";
	}
	else
	{
		$msg_info[] = "<br><br><b> Update Not Completed</b><br/>Database Not Found";		
	}
?>
<html>
<head>
<title>Merge Patient-ID And Waiting-ID in iDOC </title>
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