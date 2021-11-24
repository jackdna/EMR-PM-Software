<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");

$row_added = 0;
if($_REQUEST["row_added"]) {$row_added = $_REQUEST["row_added"]; }

if(!$_REQUEST["totalRec"]) {
	$getNumQry 	= "SELECT patient_in_waiting_id,patient_id,drOfficePatientId FROM patient_in_waiting_tbl WHERE drOfficePatientId NOT LIKE '%-%' AND drOfficePatientId!='' ORDER BY patient_in_waiting_id";
	$getNumRes 	= imw_query($getNumQry) or die(imw_error());
	$totalRec 	= imw_num_rows($getNumRes);
}else {
	$totalRec 	= $_REQUEST["totalRec"];
}
$qry 	= "SELECT patient_in_waiting_id,patient_id,drOfficePatientId FROM patient_in_waiting_tbl WHERE drOfficePatientId NOT LIKE '%-%' AND drOfficePatientId!='' ORDER BY patient_in_waiting_id LIMIT 0,2";
$res 	= imw_query($qry) or die(imw_error());
$numRow = imw_num_rows($res);
if($numRow>0) {
	while($row = imw_fetch_array($res)) {
		include("../../common/conDb.php");
		$patient_in_waiting_id 	= $row["patient_in_waiting_id"];
		$patient_id 			= $row["patient_id"];
		$drOfficePatientId 		= $row["drOfficePatientId"];
		
		$iolinkMergeId 			= $drOfficePatientId."-".$patient_id;
		$iAscMergeId 			= "drOffice".$drOfficePatientId."-".$patient_id;
		$iAscAthenaId 			= "drOffice".$drOfficePatientId;
		
		$updtWaitingTblQry 		= "UPDATE patient_in_waiting_tbl SET drOfficePatientId='".$iolinkMergeId."' WHERE patient_in_waiting_id = '".$patient_in_waiting_id."'";
		$updtWaitingTblRes 		= imw_query($updtWaitingTblQry) or die(imw_error());
		
		include("../../connect_imwemr.php");
		$updtiASCQry 			= "UPDATE patient_data SET athenaID='".$iAscMergeId."' WHERE athenaID = '".$iAscAthenaId."'";
		$updtiASCRes 			= imw_query($updtiASCQry) or die(imw_error());
	}
}
$combine_num_row = imw_num_rows($res)+$row_added;
?>
<form name="frmPtWt" method="get" action="update_iasc_patient_id.php">
	<input type="hidden" name="row_added" value="<?php echo $combine_num_row;?>">
    <input type="hidden" name="totalRec" value="<?php echo $totalRec;?>">
</form>
<?php
$color = 'green';
if($combine_num_row==0 && $totalRec==0) {
	$color = 'red';
}

$msg_info[] = "Total Record updated ".$combine_num_row." of ".$totalRec;
?>

<html>
<head>
<title>Mysql Updates After Launch</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php 
if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>

	<?php
    if($numRow>0) {?>
        <script>
			document.frmPtWt.submit();
        </script>
    <?php
    }
	@imw_close();
}
?> 
</body>
</html>







