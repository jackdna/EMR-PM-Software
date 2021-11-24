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
	echo "<br><br>Please take back up of table <b>iolink_scan_consent</b> before run this update";
	echo "<br><br><a href=\"#\" onClick=\"javascript:window.location.replace('?backup=yes');\">Run This Update</a>";
	exit();
}
if(empty($tCnt)){
	$qryCnt = "SELECT isc.scan_consent_id
			FROM iolink_scan_consent isc 
			LEFT JOIN patient_in_waiting_tbl piwt ON (piwt.patient_in_waiting_id = isc.patient_in_waiting_id)
			WHERE isc.scan1Upload != '' ORDER BY isc.scan_consent_id DESC ";
	
	$resCnt = imw_query($qryCnt) or die(imw_error());
	$tCnt = imw_num_rows($resCnt);
}
$qry = "SELECT isc.scan_consent_id,isc.image_type,isc.scan1Upload,isc.document_name,
			   piwt.surgeon_fname, piwt.surgeon_mname, piwt.surgeon_lname
		FROM iolink_scan_consent isc 
		LEFT JOIN patient_in_waiting_tbl piwt ON (piwt.patient_in_waiting_id = isc.patient_in_waiting_id)
		WHERE isc.scan1Upload != '' ORDER BY isc.scan_consent_id DESC LIMIT 0,1";
$res = imw_query($qry) or die(imw_error());
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_array($res)) {
		$scan_consent_id 		= $row["scan_consent_id"];
		$image_type 			= $row["image_type"];
		$scan1Upload 			= $row["scan1Upload"];
		
		$patientInWaitingSurgeonFname = $row["surgeon_fname"];
		$patientInWaitingSurgeonMname = $row["surgeon_mname"];
		$patientInWaitingSurgeonLname = $row["surgeon_lname"];
		if($patientInWaitingSurgeonMname){
			$patientInWaitingSurgeonMname = ' '.$patientInWaitingSurgeonMname;
		}
		$surgeonName = $patientInWaitingSurgeonFname.$patientInWaitingSurgeonMname.' '.$patientInWaitingSurgeonLname;
		$surgeonName = str_replace(" ","_",$surgeonName);
		$surgeonName = str_replace(",","",$surgeonName);
		$surgeonName = str_replace("!","",$surgeonName);
		$surgeonName = str_replace("@","",$surgeonName);
		$surgeonName = str_replace("%","",$surgeonName);
		$surgeonName = str_replace("^","",$surgeonName);
		$surgeonName = str_replace("$","",$surgeonName);
		$surgeonName = str_replace("'","",$surgeonName);
		$surgeonName = str_replace("*","",$surgeonName);

		$pdfFolderName = '../../../'.$iolinkDirectoryName.'/admin/pdfFiles/'.$surgeonName;
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
		$jpgNme = "iolink_".$scnImgChk.$scan_consent_id.".jpg";
		$jpgFilePathDatabaseSave = $pdfFolderNameSave."/".$jpgNme;
		$jpgFileFullPath 	= 	$rootServerPath.'/'.$iolinkDirectoryName.'/'.'admin/'.$jpgFilePathDatabaseSave;
		@file_put_contents($jpgFileFullPath,$scan1Upload);		
		$fileSize = filesize($jpgFileFullPath);
		$updtQry = "UPDATE iolink_scan_consent SET 
					pdfFilePath = '".$jpgFilePathDatabaseSave."',
					document_size = '".$fileSize."',
					scan1Upload = '',
					scan1Status = ''
					WHERE scan_consent_id = '".$scan_consent_id."'
					";
		$updtRes = imw_query($updtQry) or die(imw_error());
		echo "<br>Process Done ".$_GET["st"]." of ".$tCnt;
		
		$_GET["st"]=$_GET["st"]+1;	
		
		echo "<script>window.location.replace('?st=".$_GET["st"]."&tCnt=".$tCnt."&backup=".$backup."');</script>";
		exit();
		
				
	}
	
}else {
	echo "<br>Process Completed with ".$_GET["st"]." updated record(s)";	
}

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 37 run OK";

?>

<html>
<head>
<title>Update 37</title>
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







