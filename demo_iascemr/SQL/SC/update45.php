<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");
require_once("../../html2pdfnew/imgGdLaser.php");
if(!$surgeryCenterDirectoryName){ $surgeryCenterDirectoryName='SurgeryCenter';	}
if(!$iolinkDirectoryName) 		{ $iolinkDirectoryName='iOLink';				}
if(!$rootServerPath) 			{ $rootServerPath=$_SERVER['DOCUMENT_ROOT']; 	}
include_once("../../admin/classObjectFunction.php");
$objManageData = new manageData;
$tb=$_GET["st"];
$archive_search=$_GET["archive_search"];

if(empty($tb)){
	$tb=$_GET["st"]=0;
}
$backup = $_GET['backup'];
if($backup!='yes') {
	echo "<br><br>Please take back up of table <b>users</b> before run this update";
	echo "<br><br><a href=\"#\" onClick=\"javascript:window.location.replace('?backup=yes');\">Run This Update</a>";
	exit();
}
$tCnt = $_GET['tCnt'];
if(empty($tCnt)){
	$qryCnt = "SELECT usersId FROM users where `signature`!='' AND `signature_path`='' ORDER BY `usersId` DESC";
	$resCnt = imw_query($qryCnt) or die(imw_error());
	$tCnt = imw_num_rows($resCnt);
}
$qry = "SELECT usersId,signature,fname FROM users where `signature`!='' AND `signature_path`='' ORDER BY `usersId` DESC LIMIT 0,1";
$res = imw_query($qry) or die(imw_error());
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_array($res)) {
		$usersId 		= $row["usersId"];
		$signature 		= $row["signature"];
		$fname 			= $row["fname"];

		$drawingFolderUser = "../../admin/pdfFiles/user_detail";
		if(!is_dir($drawingFolderUser)){		
			mkdir($drawingFolderUser, 0777);
		}
		
		
		$pdfFolderName = "../../admin/pdfFiles/user_detail/".$fname."_".$usersId;
		$pdfFolderNameSave = "pdfFiles/user_detail/".$fname."_".$usersId;
		if(is_dir($pdfFolderName)) {
			//DO NOT CREATE FOLDER AGAIN
		}else {
			mkdir($pdfFolderName, 0777);
		}
		
		$scnImgChk = 'user_sign_'.$usersId.'_'.date('YmdHis');
		$jpgNme = $scnImgChk.".jpg";
		
		$jpgFilePathDatabaseSave = $pdfFolderNameSave."/".$jpgNme;
		$updtQry = "UPDATE users SET 
					signature_path = '".$jpgFilePathDatabaseSave."'
					WHERE usersId = '".$usersId."'
					";
		$updtRes = imw_query($updtQry) or die(imw_error());
		$jpgFileFullPath 	= 	$rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$jpgFilePathDatabaseSave;
		
		drawOnImageLaser($signature,"../../images/white_signature.jpg",$jpgNme,$pdfFolderName);		
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
$msg_info[] = "Update 45 run OK";

?>

<html>
<head>
<title>Update 45</title>
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







