<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

// This update is created to update ASCID from IDOC-Super Bill Where ASC-ID > 0 and APPT-ID = 0 
// Run in master DB after merge

	set_time_limit(0);
	include_once("../../../common/conDb.php");  //MYSQL CONNECTION
	include_once("test_update_merge_db_detail.php");  //DB Details
	
	if($IDOC_DB && $childDB)
	{
		$RECORD_UPDATE=0;
		
		$query	=	"Select ISB.idSuperBill, ST.appt_id, PC.ascId From ".$IDOC_DB.".superbill ISB 
					Join ".$childDB.".patientconfirmation PC on (ISB.ascID = PC.asc_id_before_merge And ISB.patientId = PC.imwPatientId And ISB.dateOfService = PC.dos ) 
					Join ".$childDB.".stub_tbl ST On (PC.patientConfirmationId = ST.patient_confirmation_id)
					Where ISB.ascId > 0  && ISB.sch_app_id = 0 ";
		$sql	= imw_query($query) or $msg_info[] = $query.imw_error();
		
		while($row = imw_fetch_object($sql))
		{
			$superBillId	=	trim($row->idSuperBill);
			$ascId			=	trim($row->ascId);
			$apptId			=	trim($row->appt_id);
			
			if($superBillId && $ascId && $apptId )
			{
				$qry 	=	"UPDATE ".$IDOC_DB.".superbill SET ascId =".$ascId.", sch_app_id = ".$apptId." WHERE idSuperBill  =".$superBillId." ";
				imw_query($qry) or $msg_info[] = $qry.imw_error();
			}
			$RECORD_UPDATE++;
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
<title>Merge ASC-ID in iDOC Super Bill </title>
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