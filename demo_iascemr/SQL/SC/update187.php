<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE `surgerycenter`  ADD `sx_plan_sheet_review` TINYINT(2) NOT NULL DEFAULT '1';";
$sql[] = "ALTER TABLE `operatingroomrecords` ADD `sxPlanReviewedBySurgeonChk` TINYINT( 2 ) NOT NULL , ADD INDEX ( sxPlanReviewedBySurgeonChk );";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

$qry = "SELECT operatingRoomRecordsId FROM operatingroomrecords WHERE sxPlanReviewedBySurgeonChk = '1' LIMIT 0,1";
$res = imw_query($qry)or $msg_info[] = $qry.imw_error();
if(imw_num_rows($res)==0) {
	$upQry = "UPDATE operatingroomrecords SET sxPlanReviewedBySurgeonChk = '1'  WHERE sxPlanReviewedBySurgeonChk = '0' AND version_num > '1' AND form_status != '' ";
	$upRes = imw_query($upQry)or $msg_info[] = $upQry.imw_error();
}
$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 187 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 187 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 187</title>
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