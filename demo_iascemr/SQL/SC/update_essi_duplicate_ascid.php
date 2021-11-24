<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");
if(!$surgeryCenterDirectoryName){ $surgeryCenterDirectoryName='SurgeryCenter';	}
if(!$iolinkDirectoryName) 		{ $iolinkDirectoryName='iOLink';				}
if(!$rootServerPath) 			{ $rootServerPath=$_SERVER['DOCUMENT_ROOT']; 	}
include_once("../../admin/classObjectFunction.php");
$objManageData = new manageData;
$tb=$_GET["st"];
$backup = $_GET['backup'];
if(empty($tb)){
	$tb=$_GET["st"]=0;
}
$tCnt = $_GET['tCnt'];
if($backup!='yes') {
	echo "<br><br>Please take back up of table <b>patientconfirmation</b> and <b>surgerycenter</b> in ScEMR and <b>superbill</b> in iASC before run this update";
	echo "<br><br><a href=\"#\" onClick=\"javascript:window.location.replace('?backup=yes');\">Run This Update</a>";
	exit();
}
if(empty($tCnt)){
	$qryCnt = "SELECT count( ascId ) AS total_cnt_ascid, ascId, group_concat( `patientConfirmationId` ) AS ptconfirm_ids, group_concat( `dos` ) AS surgery_dates
				FROM `patientconfirmation` 
				WHERE ascId !=0
				GROUP BY `ascId` 
				HAVING count( ascId ) >1 ORDER BY ascId ASC";
	
	$resCnt = imw_query($qryCnt) or die(imw_error().$qryCnt);
	$tCnt = imw_num_rows($resCnt);
}
$qry = "SELECT count( ascId ) AS total_cnt_ascid, ascId, group_concat( `patientConfirmationId` ) AS ptconfirm_ids, group_concat( `dos` ) AS surgery_dates
				FROM `patientconfirmation` 
				WHERE ascId !=0
				GROUP BY `ascId` 
				HAVING count( ascId ) >1 ORDER BY ascId ASC  LIMIT 0,1";
$res = imw_query($qry) or die(imw_error().$qry);
if(imw_num_rows($res)>0) {
	$row = imw_fetch_array($res);
	$total_cnt_ascid 		= $row["total_cnt_ascid"];
	$ascId 					= $row["ascId"];
	$ptconfirm_ids 			= $row["ptconfirm_ids"];
	$surgery_dates 			= $row["surgery_dates"];

	$scQry = "SELECT ascId_present+1 as new_ascid FROM surgerycenter ORDER BY surgeryCenterId LIMIT 0,1";
	$scRes = imw_query($scQry) or die(imw_error());
	$scRow = imw_fetch_array($scRes);
	$new_ascid = $scRow["new_ascid"];

	list($confirm_id1,$confirm_id2) = explode(",",$ptconfirm_ids);
	if($confirm_id2 && $new_ascid) {
		$updtQry 	= "UPDATE patientconfirmation SET 
						ascId = '".$new_ascid."'
						WHERE patientConfirmationId = '".$confirm_id2."'
					";
		$updtRes 	= imw_query($updtQry) or die(imw_error().$updtQry);	
		$updtScQry 	= "UPDATE surgerycenter SET 
						ascId_present = '".$new_ascid."'
						WHERE 1
					";
		$updtScRes 	= imw_query($updtScQry) or die(imw_error().$updtScQry);
		
		$getiAScPatientIdQry = "SELECT pt.imwPatientId, pt.patient_id 
								FROM patientconfirmation pc
								INNER JOIN patient_data_tbl pt ON (pt.patient_id = pc.patientId)
								WHERE pc.patientConfirmationId = '".$confirm_id2."';
							   ";
		$getiAScPatientIdRes = imw_query($getiAScPatientIdQry) or die(imw_error().$getiAScPatientIdQry);
		if(imw_num_rows($getiAScPatientIdRes)>0) {
			$getiAScPatientIdRow = imw_fetch_array($getiAScPatientIdRes);
			$iAScPatientId = $getiAScPatientIdRow["imwPatientId"];
			if(trim($iAScPatientId)) {
				include_once('../../connect_imwemr.php'); // imwemr connection
				$updtSuperBillQry 	= "UPDATE superbill SET 
										ascId = '".$new_ascid."'
										WHERE patientId = '".$iAScPatientId."' AND ascId = '".$ascId."'
									  ";
				$updtSuperBillRes 	= imw_query($updtSuperBillQry) or die(imw_error().$updtSuperBillQry);
			}
		}
				
	}
	echo "<br>Process Done ".$_GET["st"]." of ".$tCnt;
	
	$_GET["st"]=$_GET["st"]+1;	
	
	echo "<script>window.location.replace('?st=".$_GET["st"]."&tCnt=".$tCnt."&backup=".$backup."');</script>";
	exit();
	
}else {
	echo "<br>Process Completed with ".$_GET["st"]." updated record(s)";	
}

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update ESSI Duplicate ASCID run OK";

?>

<html>
<head>
<title>Update ESSI Duplicate ASCID</title>
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







