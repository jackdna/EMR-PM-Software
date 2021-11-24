<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include("../../common/conDb.php");
include("../../admin/classObjectFunction.php");

$objManageData = new manageData;

$indexStart	=	isset($_REQUEST["c"])	?	$_REQUEST["c"]	:	0	;
$totalCount =	isset($_REQUEST['cn'])	?	$_REQUEST['cn']	:	''	;	


if(empty($totalCount))
{
	
	//START TAKE BACKUP OF TABLE BEFORE ANY ACTION
	imw_query("CREATE TABLE narcotics_data_tbl_bak_".date("d_m_Y")." LIKE narcotics_data_tbl") or $msg_info[] = imw_error();
	imw_query("INSERT INTO  narcotics_data_tbl_bak_".date("d_m_Y")." (SELECT *  FROM narcotics_data_tbl)") or $msg_info[] = imw_error();
	//END TAKE BACKUP OF TABLE BEFORE ANY ACTION
	
	//START TRUNCATE TABLE NARCOTICS DATA BEFORE RUNNING THIS SPECIAL UPDATE
	$truncateQry = " TRUNCATE TABLE narcotics_data_tbl ";
	$truncateRes 	= imw_query($truncateQry) or die(imw_error().$truncateQry);
	//END TRUNCATE TABLE NARCOTICS DATA BEFORE RUNNING THIS SPECIAL UPDATE
	
	$qryCnt = "SELECT count( pc.patientConfirmationId ) AS totalConfIds FROM `patientconfirmation` pc 
							INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.chartSignedByAnes = 'green')
							WHERE pc.patientConfirmationId !='0'
							ORDER BY pc.patientConfirmationId ASC";
	
	$resCnt 	= imw_query($qryCnt) or die(imw_error().$qryCnt);
	$res		= imw_fetch_object($resCnt);
	$totalCount = $res->totalConfIds;
	
	//echo "<br><br><a href=\"#\" onClick=\"javascript:window.location.replace('?cn=".$totalCount."');\">Click Here for Confirmation to run this update</a>";
	//exit();
	
}

if($totalCount > 0 )
{
	
	$userInitialsArr = $objManageData->userInitialsArrFun();	
	
	$fetchRecords	=	500;
	$qry = "SELECT pc.patientConfirmationId, if(ndt.countRecords,ndt.countRecords,0) as narcoCount FROM `patientconfirmation` pc
						INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.chartSignedByAnes = 'green')
						LEFT JOIN (Select confirmation_id,count(confirmation_id) as countRecords From narcotics_data_tbl Group By confirmation_id) ndt
						ON ndt.confirmation_id = pc.patientConfirmationId
						WHERE pc.patientConfirmationId !='0'
						ORDER BY pc.patientConfirmationId ASC
						Limit ".$indexStart.", ".$fetchRecords." "; 
	
	
	$res = imw_query($qry) or die(imw_error().$qry);
	
	if(imw_num_rows($res)>0)
	{
		
		while($row	=	imw_fetch_object($res))
		{
			$confirmationID	=	$row->patientConfirmationId;
			$existInNarcoTbl=	($row->narcoCount > 0) ? true : false;
			
			if(!$existInNarcoTbl)
			{
				$objManageData->calculate_narcotics_data($confirmationID,$userInitialsArr);		
			}
			
			$indexStart++;
			
		}
		
		echo "<br>Process Done ".$indexStart." of ".$totalCount.'<br>';
		echo "<script>window.location.replace('?c=".$indexStart."&cn=".$totalCount."');</script >";
		exit;
		
	}
	
	else
	{
		echo "<br>Process Completed with ".$indexStart." updated record(s)";	
	}
	
	
	
}
else {
	echo "<br>Process Completed with ".$indexStart." updated record(s)";	
}

$color = 'green';
if(count($msg_info)>0){ $color = 'red'; }
$msg_info[] = "Narcotics data extraction completed for previous records - run OK";

?>

<html>
<head>
<title>Update - Narcotics data extraction for Previous Records</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>