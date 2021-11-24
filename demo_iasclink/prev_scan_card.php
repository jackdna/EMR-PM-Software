<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<LINK HREF="css/style_surgery.css" TYPE="text/css" REL="stylesheet">
<LINK HREF="css/sfdc_header.css" TYPE="text/css" REL="stylesheet">
<style>
.body_c {
	scrollbar-face-color:#408bc4;
	scrollbar-shadow-color:#afefff;
	scrollbar-highlight-color:#afefff;
	scrollbar-3dlight-color:#000000;
	scrollbar-darkshadow-color:#006399;
	scrollbar-track-color:#bfd3e6;
	scrollbar-arrow-color:#FFFFFF;
	margin-top:0;
	margin-left:0;
	margin-right:0;
}
</style>
<script type="text/javascript" src="js/jsFunction.js"></script>
<?php include_once("common/conDb.php"); ?>
<?php
$fdr_pat_img="imedic_uploaddir";
$pid = $_REQUEST['patient_id'];
$isRecordExists = $_REQUEST['isRecordExists'];
$currentCaseid = $_REQUEST['currentCaseid'];
$waiting_id = $_REQUEST['waiting_id'];
$patient_id = $_REQUEST['patient_id'];
$ty = $_REQUEST['ty'];
if($_REQUEST["img_fin_sig"]<>""){			
	$imgpathr_f=$_REQUEST["img_fin_sig"];
	if(trim($imgpathr_f)!=""){
		if(file_exists($imgpathr_f)){			
			@unlink($imgpathr_f);
			$msg="Licence Scan Deleted Sucessfully!";			
			$vquery2 = "delete from scans where id = $idd";															
			$vsql2 = imw_query($vquery2);
			if(!$vsql2)
			echo ("Error : ".imw_error());			
		}
	}
}

if($isRecordExists){
	require_once('prev_scan_card1.php');
}
else{
	require_once('prev_scan_card2.php');
}

?>