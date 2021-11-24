<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
	set_time_limit(0);
	include_once("../../../common/conDb.php");  //MYSQL CONNECTION
	include_once("test_update_merge_db_detail.php");  //DB Details
	
	if($IDOC_DB)
	{
		imw_query("CREATE TABLE ".$IDOC_DB.".superbill_bak_".date("d_m_Y")." AS (SELECT * FROM ".$IDOC_DB.".superbill)") or $msg_info[] = imw_error();
		imw_query("CREATE TABLE ".$IDOC_DB.".pn_reports_bak_".date("d_m_Y")." AS (SELECT * FROM ".$IDOC_DB.".pn_reports)") or $msg_info[] = imw_error();
		
		$RECORD_UPDATE=0;
		
		$scemrQuery = imw_query("SELECT pc.ascId, st.appt_id FROM patientconfirmation pc join stub_tbl st on (st.patient_confirmation_id = pc.patientConfirmationId) Where st.appt_id <> '' and pc.ascId <> '0' ORDER BY st.stub_id ASC ") or $msg_info[] = $scemrQuery.imw_error();
		if(imw_error())$msg_info[] = imw_error();
		while($scemrData = imw_fetch_object($scemrQuery))
		{
			$idoc_apptId	=	trim($scemrData->appt_id);
			$scemr_ascId	=	trim($scemrData->ascId);
			
			if(trim($scemr_ascId)){
				$qry 	=	"UPDATE ".$IDOC_DB.".superbill SET ascId =".$scemr_ascId." WHERE sch_app_id  ='".$idoc_apptId."' And sch_app_id <> '0' ";
				imw_query($qry) or $msg_info[] = $qry.imw_error();
			}
			$RECORD_UPDATE++;
		}

		//START RESET (JUMP) OPERATIVE NOTES ID IN IDOC
		$scemrStubQuery = imw_query("SELECT COLLATION_NAME FROM information_schema.columns WHERE TABLE_NAME = 'stub_tbl' AND COLUMN_NAME = 'appt_id' LIMIT 0,1") or $msg_info[] = $scemrStubQuery.imw_error();
		if(imw_error())$msg_info[] = imw_error();
		$scemrStubRow 	= imw_fetch_object($scemrStubQuery);
		$collationName	=	trim($scemrStubRow->COLLATION_NAME);
		if(!$collationName) {
			$collationName = "latin1_general_ci";	
		}
		$idocPnReportQry = "ALTER TABLE ".$IDOC_DB.".pn_reports CHANGE `sc_emr_iasc_appt_id` `sc_emr_iasc_appt_id` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE ".$collationName." NOT NULL ";
		$idocPnReportRes = imw_query($idocPnReportQry) or $msg_info[] = $idocPnReportQry.imw_error();
		
		$idocScemrMasterOprtIdQry = "UPDATE ".$IDOC_DB.".pn_reports p, ".$masterDB.".stub_tbl s, ".$masterDB.".operativereport o
									SET p.sc_emr_operative_report_id = o.oprativeReportId
									WHERE p.sc_emr_iasc_appt_id = s.appt_id
									AND s.patient_confirmation_id = o.confirmation_id AND s.patient_confirmation_id !='' AND s.patient_confirmation_id !='0'
									AND p.sc_emr_iasc_appt_id != '' ";
		$idocScemrMasterOprtIdRes = imw_query($idocScemrMasterOprtIdQry) or $msg_info[] = $idocScemrMasterOprtIdQry.imw_error();
		
		$idocScemrChildOprtIdQry = "UPDATE ".$IDOC_DB.".pn_reports p, ".$childDB.".stub_tbl s, ".$childDB.".operativereport o
									SET p.sc_emr_operative_report_id = o.oprativeReportId
									WHERE p.sc_emr_iasc_appt_id = s.appt_id
									AND s.patient_confirmation_id = o.confirmation_id AND s.patient_confirmation_id !='' AND s.patient_confirmation_id !='0'
									AND p.sc_emr_iasc_appt_id != '' ";
		$idocScemrChildOprtIdRes = imw_query($idocScemrChildOprtIdQry) or $msg_info[] = $idocScemrChildOprtIdQry.imw_error();
		//END RESET (JUMP) OPERATIVE NOTES ID IN IDOC
		
		
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