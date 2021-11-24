<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

include_once("../../common/conDb.php");
include_once("../../admin/classObjectFunction.php");
$objManageData = new manageData;

include_once("../../signAllDefaultMedications.php");

imw_query("CREATE TABLE preopphysicianorders_bak_".date("d_m_Y")." LIKE preopphysicianorders") or $msg_info[] = imw_error();
imw_query("INSERT INTO  preopphysicianorders_bak_".date("d_m_Y")." (SELECT *  FROM preopphysicianorders)") or $msg_info[] = imw_error();
//$pConfId,$primaryProcedureCatId,$surgeon_id,$procedureId,$secProcedureId,$terProcedureId,$preOpTableName = 'preopphysicianorders'
$qry = "SELECT 
		pc.patient_primary_procedure_id, pc.patient_secondary_procedure_id, pc.patient_tertiary_procedure_id,pc.surgeonId,
		pc.patientConfirmationId,
		pcr.catId as procedureCatId 
		FROM preopphysicianorders pp 
		INNER JOIN patientconfirmation pc ON (pc.patientConfirmationId = pp.patient_confirmation_id)
		INNER JOIN procedures pcr ON (pc.patient_primary_procedure_id = pcr.procedureId )
		WHERE pp.form_status !='' AND pp.saveFromChart = '1' 
		ORDER BY pc.dos DESC, pc.surgery_time ASC";
$res = imw_query($qry) or die($qry.imw_error());
$a = 0;
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_array($res)) {
		$defaultMedArr		= array();
		$medications		= array();
		$defaultMedArr 		= signAllDefautlMedications($row["patientConfirmationId"], $row["procedureCatId"], $row["surgeonId"], $row["patient_primary_procedure_id"], $row["patient_secondary_procedure_id"], $row["patient_tertiary_procedure_id"],"preopphysicianorders");
		$medications		= $defaultMedArr[0];
		$otherPreOpOrders	= $defaultMedArr[1];
		$updtQry = "UPDATE preopphysicianorders SET preOpOrdersOther = IF(preOpOrdersOther!='', preOpOrdersOther, '".addslashes($otherPreOpOrders)."'), saveFromChart = '0'  WHERE patient_confirmation_id = '".$row["patientConfirmationId"]."' ";	
		$updtRes = imw_query($updtQry) or die($updtQry.imw_error());
		if(imw_affected_rows()>0) {
			$a++;	
		}
	}
}
$new_info[] = "Total ".$a." Of ".imw_num_rows($res)." Updated";

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 144 Failed!</b><br>".implode("<br>",$msg_info)."<br>".implode("<br>",$new_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 144 Success.</b><br>".implode("<br>",$new_info)."<br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 144</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
@imw_close();
}
?> 
</body>
</html>