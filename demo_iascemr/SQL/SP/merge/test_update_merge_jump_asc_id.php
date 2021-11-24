<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
	set_time_limit(0);
	include_once("../../../common/conDb.php");  //MYSQL CONNECTION
	include_once("test_update_merge_db_detail.php");  //DB Details
	$msg_info = array();
	if($masterDB && $childDB)
	{
		
		$query	=	"Select jump_merged_asc_id_status From $masterDB.surgerycenter WHERE surgeryCenterId = '1' AND jump_merged_asc_id_status = '0' ";
		$sql	=	imw_query($query) or $msg_info[] = $query. '<br>Error occured at line no. '.(__LINE__).': '.imw_error();
		$cnt	=	imw_num_rows($sql);
		
		if($cnt == 0 ) {	die(implode(",",$msg_info).'This update is already executed');  }
		
		$RECORD_UPDATE=0;
		$LastDB		=	end($DB_ARRAY);
		foreach($DB_ARRAY as $master => $child)
		{
			if($master && $child)
			{
				imw_query("CREATE TABLE $master.patientconfirmation_bak_".date("d_m_Y")." AS (SELECT * FROM $master.patientconfirmation)") or $msg_info[] = imw_error();
				imw_query("CREATE TABLE $child.patientconfirmation_bak_".date("d_m_Y")." AS (SELECT * FROM $child.patientconfirmation)") or $msg_info[] = imw_error();
				
				$maxQuery	=	"Select MAX(ascId) as MAX_ASC From ".$master.".patientconfirmation ";
				$maxSql		=	imw_query($maxQuery) or $msg_info[] = $maxQuery. '<br>Error occured at line no. '.(__LINE__).': '.imw_error();
				$maxRes		=	imw_fetch_object($maxSql);
				$maxASC_ID	=	$maxRes->MAX_ASC;
				
				$query		=	"Update $child.patientconfirmation Set asc_id_before_merge = IF(asc_id_before_merge > 0, asc_id_before_merge, ascId), ascId = ascId+$maxASC_ID WHERE ascId > 0 ";
				$sql		=	imw_query($query) or $msg_info[] = $query. '<br>Error occured at line no. '.(__LINE__).': '.imw_error();
				
				$maxQuery1	=	"Update $child.surgerycenter Set ascId_present = (Select MAX(ascId) From $child.patientconfirmation) WHERE surgeryCenterId = '1' ";
				$maxSql1	=	imw_query($maxQuery1) or $msg_info[] = $maxQuery1. '<br>Error occured at line no. '.(__LINE__).': '.imw_error();
				
				$updateVSQ	=	"Update $master.vision_success V, $master.patientconfirmation P Set V.ascId = P.ascId WHERE P.patientConfirmationId = V.confirmation_id ";
				$updateVSS	=	imw_query($updateVSQ) or $msg_info[] = $updateVSQ. '<br>Error occured at line no. '.(__LINE__).': '.imw_error();
				
			}
		}
		
		if($masterDB && $LastDB)
		{
			$maxQueryL	=	"Update $masterDB.surgerycenter Set jump_merged_asc_id_status ='1', ascId_present = (Select MAX(ascId) From $LastDB.patientconfirmation) WHERE surgeryCenterId = '1'";
			$maxSql		=	imw_query($maxQueryL) or $msg_info[] = $maxQueryL. '<br>Error occured at line no. '.(__LINE__).': '.imw_error();
			
		}
		
		$msg_info[] = "<br><br><b> Update Completed</b><br/>ASC-ID Jump Records Updated.";
	}
	else
	{
		$msg_info[] = "<br><br><b> Update Not Completed</b><br/>Database Not Found";		
	}
?>
<html>
<head>
<title>Jump ASC-ID </title>
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