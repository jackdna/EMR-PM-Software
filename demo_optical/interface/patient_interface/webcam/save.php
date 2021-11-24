<?php
require_once(dirname('__FILE__')."/../../../config/config.php");
extract($_REQUEST);
//if(isset($GLOBALS["HTTP_RAW_POST_DATA"])){
$jpg = $GLOBALS["HTTP_RAW_POST_DATA"];

$pId = $_SESSION['patient_session_id'];

$upDir = dirname(__FILE__)."/../uploaddir";
$pDir = "/PatientId_".$pId;

$ptdir = $upDir.$pDir;
if(!is_dir($upDir)){
	mkdir($upDir,0700);
}

if(!is_dir($ptdir)){
	mkdir($ptdir,0700);
}
if($pId != ""){
	$filename = $upDir.$pDir."/Imedic_". mktime(). ".jpg";
	$fileContent = file_put_contents($filename, $jpg);
	$dbFile = "uploaddir".$pDir."/Imedic_". mktime(). ".jpg";
	$qry = "insert into in_patient_pictures set patient ='".$_SESSION['patient_session_id']."' , image ='".$dbFile."'";
	$res = imw_query($qry);
	$img_id = imw_insert_id();
	//}
	$_SESSION['img_src'] = $dbFile;
	$_SESSION['img_id'] = $img_id;
}else{
	$_SESSION['img_src'] = '';
	$_SESSION['img_id'] = '';
}
?>
