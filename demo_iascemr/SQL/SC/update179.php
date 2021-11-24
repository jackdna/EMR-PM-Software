<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql1="ALTER TABLE `operatingroomrecords` ADD INDEX sxPlanReviewedBySurgeon(sxPlanReviewedBySurgeon)"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `operatingroomrecords` ADD `version_num` INT( 11 ) NOT NULL , ADD INDEX version_num(version_num)"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `operatingroomrecords` ADD `version_date_time` DATETIME NOT NULL ;"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1=" UPDATE `operatingroomrecords` Set version_num = '1', version_date_time = '".date('Y-m-d H:i:s')."' 
				Where (form_status = 'completed' || form_status = 'not completed') And version_num = '0' And sxPlanReviewedBySurgeon = '' ";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1=" UPDATE `operatingroomrecords` Set version_num = '2', version_date_time = '".date('Y-m-d H:i:s')."' 
				Where (form_status = 'completed' || form_status = 'not completed') And version_num = '0' And sxPlanReviewedBySurgeon != '' ";
imw_query($sql1)or $msg_info[] = imw_error();

if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 179 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 179 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 179</title>
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