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
	include('../../connect_imwemr.php');
	imw_query("CREATE TABLE superbill_bak_scemr AS (SELECT * FROM superbill)")or $msg_info[] = imw_error();	

	imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
	include("../../common/conDb.php");  //SURGERYCENTER CONNECTION	
	
	$qryCnt = "SELECT count( pc.patientConfirmationId ) AS totalConfIds FROM `patientconfirmation` pc 
						INNER JOIN `stub_tbl`  st ON (st.patient_confirmation_id = pc.patientConfirmationId)
						WHERE st.appt_id != ''
						ORDER BY pc.patientConfirmationId ASC";
	
	$resCnt 	= imw_query($qryCnt) or die(imw_error().$qryCnt);
	$res		= imw_fetch_object($resCnt);
	$totalCount = $res->totalConfIds;
	
	//echo "<br><br><a href=\"#\" onClick=\"javascript:window.location.replace('?cn=".$totalCount."');\">Click Here for Confirmation to run this update</a>";
	//exit();
	
}

if($totalCount > 0 )
{
	$fetchRecords	=	500;
	$qry = "SELECT st.appt_id, st.nextGenPersonId, pc.ascId FROM `patientconfirmation` pc 
						INNER JOIN `stub_tbl`  st ON (st.patient_confirmation_id = pc.patientConfirmationId)
						WHERE st.appt_id != ''
						ORDER BY pc.patientConfirmationId ASC
						Limit ".$indexStart.", ".$fetchRecords." "; 
	
	
	$res = imw_query($qry) or die(imw_error().$qry);
	
	if(imw_num_rows($res)>0)
	{
		
		while($row	=	imw_fetch_object($res))
		{
			$appt_id			=	$row->appt_id;
			$nextGenPersonId	=	$row->nextGenPersonId;
			$ascId				=	$row->ascId;
			
			include('../../connect_imwemr.php');
			$updtQry = "UPDATE superbill SET sch_app_id = '".$appt_id."' WHERE patientId = '".$nextGenPersonId."' AND ascId = '".$ascId."'  AND ascId != '0' AND sch_app_id = '0' ";
			$updtRes = imw_query($updtQry) or die(imw_error().$updtQry);			

			$indexStart++;
		
			echo "<br>Process Done ".$indexStart." of ".$totalCount.'<br>';
			echo '<br><br>';	
		}
		
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
$msg_info[] = "Appointment-ID update in superbill for previous record - run OK";

?>

<html>
<head>
<title>Update - Implementing Supplies for Previous Records</title>
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