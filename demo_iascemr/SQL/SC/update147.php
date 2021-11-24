<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");
if(!$surgeryCenterDirectoryName){ $surgeryCenterDirectoryName='SurgeryCenter';	}
if(!$iolinkDirectoryName) 		{ $iolinkDirectoryName='iOLink';				}
if(!$rootServerPath) 			{ $rootServerPath=$_SERVER['DOCUMENT_ROOT']; 	}
include_once("../../admin/classObjectFunction.php");
$objManageData = new manageData;
$tb=$_GET["st"];
$archive_search=$_GET["archive_search"];
$archiveScanDbName='';
if(constant('ARCHIVE_SCAN_DB') && $archive_search=='yes') {
	$archiveScanDbName = constant('ARCHIVE_SCAN_DB').".";
}
if(empty($tb)){
	$tb=$_GET["st"]=0;
}
$backup = $_GET['backup'];
if($backup!='yes') {
	echo "<br><br>Please take back up of table <b>iolink_scan_consent</b> before run this update";
	echo "<br><br><a href=\"#\" onClick=\"javascript:window.location.replace('?backup=yes');\">Run This Update</a>";
	exit();
}
$tCnt = $_GET['tCnt'];
if(empty($tCnt)){
	$qryCnt = "SELECT scan_consent_id FROM ".$archiveScanDbName."iolink_scan_consent where `scan1Upload`!='' ORDER BY `scan_consent_id` DESC";
	$resCnt = imw_query($qryCnt) or die(imw_error());
	$tCnt = imw_num_rows($resCnt);
}
$qry = "SELECT scan_consent_id,image_type,scan1Upload,patient_in_waiting_id FROM ".$archiveScanDbName."iolink_scan_consent where `scan1Upload`!='' ORDER BY `scan_consent_id` DESC LIMIT 0,1";
$res = imw_query($qry) or die(imw_error());
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_array($res)) {
		$scan_consent_id 		= $row["scan_consent_id"];
		$image_type 			= $row["image_type"];
		$scan1Upload 			= $row["scan1Upload"];
		$patient_in_waiting_id 	= $row["patient_in_waiting_id"];
		
		if($patient_in_waiting_id) {
			// GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
				$surgeonData = $objManageData->getRowRecord('patient_in_waiting_tbl', 'patient_in_waiting_id', $patient_in_waiting_id);
				$surgeonFname = $surgeonData->surgeon_fname;
				$surgeonMname = $surgeonData->surgeon_mname;
				$surgeonLname = $surgeonData->surgeon_lname;
				if($surgeonMname){
					$surgeonMname = ' '.$surgeonMname;
				}
				$surgeonName = $surgeonFname.$surgeonMname.' '.$surgeonLname;
				
			// END GET SURGEON NAME FOR GIVEN WAITING ID 
		}else {
			$surgeonName = "iolink_files";	
		}

		$surgeonName = str_replace(" ","_",$surgeonName);
		$surgeonName = str_replace(",","",$surgeonName);
		$surgeonName = str_replace("!","",$surgeonName);
		$surgeonName = str_replace("@","",$surgeonName);
		$surgeonName = str_replace("%","",$surgeonName);
		$surgeonName = str_replace("^","",$surgeonName);
		$surgeonName = str_replace("$","",$surgeonName);
		$surgeonName = str_replace("'","",$surgeonName);
		$surgeonName = str_replace("*","",$surgeonName);
		
		//$pdfFolderName = '../../../'.$iolinkDirectoryName.'/admin/pdfFiles/'.$surgeonName;
		$pdfFolderName = $rootServerPath.'/'.$iolinkDirectoryName.'/admin/pdfFiles/'.$surgeonName;
		$pdfFolderNameSave = 'pdfFiles/'.$surgeonName;
		if(is_dir($pdfFolderName)) {
			//DO NOT CREATE FOLDER AGAIN
		}else {
			mkdir($pdfFolderName, 0777);
		}
		
		$scnImgChk = "scan_";
		if(stristr($image_type,'image')) {
			$scnImgChk = "image_";	
		}
		$jpgNme = $scnImgChk.$scan_consent_id.".jpg";
		if($scan_consent_id!=0) {
			$jpgNme = "iolink_".$scnImgChk.$scan_consent_id.".jpg";
		}
		$jpgFilePathDatabaseSave = $pdfFolderNameSave."/".$jpgNme;
		$jpgFileFullPath 	= 	$rootServerPath.'/'.$iolinkDirectoryName.'/'.'admin/'.$jpgFilePathDatabaseSave;
		@file_put_contents($jpgFileFullPath,$scan1Upload);	
		if(file_exists($jpgFileFullPath)) {
			$updtQry = "UPDATE ".$archiveScanDbName."iolink_scan_consent SET 
						pdfFilePath = '".$jpgFilePathDatabaseSave."',
						scan1Upload = ''
						WHERE scan_consent_id = '".$scan_consent_id."'
						";
			$updtRes = imw_query($updtQry) or die(imw_error());
			echo "<br>Process Done ".$_GET["st"]." of ".$tCnt;
			$_GET["st"]=$_GET["st"]+1;	
			
			echo "<script>window.location.replace('?st=".$_GET["st"]."&tCnt=".$tCnt."&backup=".$backup."&archive_search=".$archive_search."');</script>";
			exit();
		}else {
			echo "Path not exists ".$jpgFileFullPath;
			die();	
		}
				
	}
	
}else {
	echo "<br>Process Completed with ".$_GET["st"]." of ".$tCnt." updated record(s)";	
}

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 147 run OK";

?>

<html>
<head>
<title>Update 147</title>
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







