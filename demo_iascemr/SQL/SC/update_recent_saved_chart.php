<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");
include_once("../../admin/classObjectFunction.php");
$objManageData = new manageData;
$affectedRows="";
$updateStubTblQry = "UPDATE stub_tbl SET recentChartSaved = '' WHERE patient_confirmation_id='0'";
$updateStubTblRes = imw_query($updateStubTblQry) or $msg_info[] = imw_error();
$affectedRows.= "Affected row for first query = ".imw_affected_rows()."<br>";
$updateStubTblSecQry = "UPDATE stub_tbl st, preopnursingrecord prn, postopnursingrecord psn, operatingroomrecords opr  
						SET st.recentChartSaved = '' 
						WHERE st.patient_confirmation_id!='0'
						AND prn.preopNurseTime=''
						AND psn.postOpSiteTime='00:00:00'
						AND opr.surgeryTimeIn=''
						AND st.patient_confirmation_id=prn.confirmation_id
						AND st.patient_confirmation_id=psn.confirmation_id
						AND st.patient_confirmation_id=opr.confirmation_id
						";
$updateStubTblSecRes = imw_query($updateStubTblSecQry) or $msg_info[] = imw_error();
$affectedRows.= "<br>Affected row for second query = ".imw_affected_rows()."<br>";


$nrsConfIdArr = $stConfIdArr = array();$stConfId=0;
$selNrsQry = "SELECT confirmation_id FROM preopnursingrecord ORDER BY confirmation_id";
$selNrsRes = imw_query($selNrsQry);
if(imw_num_rows($selNrsRes)>0) {
	while($selNrsRow = imw_fetch_array($selNrsRes)) {
		$nrsConfIdArr[] = $selNrsRow['confirmation_id'];
	}
}
$selStubTblQry = "SELECT patient_confirmation_id FROM stub_tbl WHERE recentChartSaved!='' AND patient_confirmation_id!='0' ORDER BY patient_confirmation_id";
$selStubTblRes = imw_query($selStubTblQry);
if(imw_num_rows($selStubTblRes)>0) {
	while($selStubTblRow = imw_fetch_array($selStubTblRes)) {
		if(!in_array($selStubTblRow['patient_confirmation_id'],$nrsConfIdArr)) {
			$stConfIdArr[] = $selStubTblRow['patient_confirmation_id'];
		}
	}
	if(count($stConfIdArr)>0) {
		$stConfId = implode(",",$stConfIdArr);
	}
}
$updateStubTblThirdQry = "UPDATE stub_tbl SET recentChartSaved = '' WHERE patient_confirmation_id IN(".$stConfId.") AND patient_confirmation_id!='0'";
$updateStubTblThirdRes = imw_query($updateStubTblThirdQry) or $msg_info[] = imw_error().$updateStubTblThirdQry;
$affectedRows.= "<br>Affected row for third query = ".imw_affected_rows()."<br>";


$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}

$msg_info[] = $affectedRows;
$msg_info[] = " Recent chart saved functionality corrected successfully";
?>

<html>
<head>
<title>Mysql Updates For recent chart saved</title>
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







