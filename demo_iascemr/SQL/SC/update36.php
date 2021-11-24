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
	echo "<br><br>Please take back up of table <b>scan_upload_tbl</b> before run this update";
	echo "<br><br><a href=\"#\" onClick=\"javascript:window.location.replace('?backup=yes');\">Run This Update</a>";
	exit();
}
$tCnt = $_GET['tCnt'];
if(empty($tCnt)){
	$qryCnt = "SELECT scan_upload_id FROM ".$archiveScanDbName."scan_upload_tbl where `img_content`!='' ORDER BY `scan_upload_id` DESC";
	$resCnt = imw_query($qryCnt) or die(imw_error());
	$tCnt = imw_num_rows($resCnt);
}
$qry = "SELECT scan_upload_id,image_type,img_content,confirmation_id,stub_id,document_name,iolink_scan_consent_id FROM ".$archiveScanDbName."scan_upload_tbl where `img_content`!='' ORDER BY `scan_upload_id` DESC LIMIT 0,1";
$res = imw_query($qry) or die(imw_error());
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_array($res)) {
		$scan_upload_id 		= $row["scan_upload_id"];
		$image_type 			= $row["image_type"];
		$img_content 			= $row["img_content"];
		$pconfirmId 			= $row["confirmation_id"];
		$ptStubId 				= $row["stub_id"];
		$iolink_scan_consent_id = $row["iolink_scan_consent_id"];
		if($pconfirmId) {
			// GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
				$surgeonData = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pconfirmId);
				$surgeonName = $surgeonData->surgeon_name;
			// END GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
		}else {
			//GET SURGEON NAME FROM STUB TABLE
				$stubTblSurgeonData = $objManageData->getRowRecord('stub_tbl', 'stub_id', $ptStubId);
				$stubTblSurgeonFname = $stubTblSurgeonData->surgeon_fname;
				$stubTblSurgeonMname = $stubTblSurgeonData->surgeon_mname;
				$stubTblSurgeonLname = $stubTblSurgeonData->surgeon_lname;
				if($stubTblSurgeonMname){
					$stubTblSurgeonMname = ' '.$stubTblSurgeonMname;
				}
				$surgeonName = $stubTblSurgeonFname.$stubTblSurgeonMname.' '.$stubTblSurgeonLname;
			//END SURGEON NAME FROM STUB TABLE
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
		
		
		$pdfFolderName = '../../admin/pdfFiles/'.$surgeonName;
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
		$jpgNme = $scnImgChk.$scan_upload_id.".jpg";
		if($iolink_scan_consent_id!=0) {
			$jpgNme = "iolink_".$scnImgChk.$iolink_scan_consent_id.".jpg";
		}
		$jpgFilePathDatabaseSave = $pdfFolderNameSave."/".$jpgNme;
		$updtQry = "UPDATE ".$archiveScanDbName."scan_upload_tbl SET 
					pdfFilePath = '".$jpgFilePathDatabaseSave."',
					img_content = ''
					WHERE scan_upload_id = '".$scan_upload_id."'
					";
		$updtRes = imw_query($updtQry) or die(imw_error());
		$jpgFileFullPath 	= 	$rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$jpgFilePathDatabaseSave;
		@file_put_contents($jpgFileFullPath,$img_content);	
		echo "<br>Process Done ".$_GET["st"]." of ".$tCnt;
		
		$_GET["st"]=$_GET["st"]+1;	
		
		echo "<script>window.location.replace('?st=".$_GET["st"]."&tCnt=".$tCnt."&backup=".$backup."&archive_search=".$archive_search."');</script>";
		exit();
		
				
	}
	
}else {
	echo "<br>Process Completed with ".$_GET["st"]." updated record(s)";	
}

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Update 36 run OK";

?>

<html>
<head>
<title>Update 36</title>
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







