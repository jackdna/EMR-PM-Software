<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$curDate=date("d_m_Y");
$tablename="localanesthesiarecord_".$curDate;

//create a copy of a table
imw_query("CREATE TABLE ".$tablename." ENGINE = MyISAM AS (SELECT * FROM localanesthesiarecord)")or $msg_info[] = imw_error();

$sql1 = "UPDATE `localanesthesiarecord` SET orStartTime = concat('12', substring(orStartTime, 3, length(orStartTime)-2)) WHERE orStartTime LIKE '24%'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1 = "UPDATE `localanesthesiarecord` SET orStopTime = concat('12', substring(orStopTime, 3, length(orStopTime)-2)) WHERE orStopTime LIKE '24%'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1 = "UPDATE `localanesthesiarecord` SET startTime = concat('12', substring(startTime, 3, length(startTime)-2)) WHERE startTime LIKE '24%'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1 = "UPDATE `localanesthesiarecord` SET stopTime = concat('12', substring(stopTime, 3, length(stopTime)-2)) WHERE stopTime LIKE '24%'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1 = "UPDATE `localanesthesiarecord` SET newStartTime1 = concat('12', substring(newStartTime1, 3, length(newStartTime1)-2)) WHERE newStartTime1 LIKE '24%'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1 = "UPDATE `localanesthesiarecord` SET newStopTime1 = concat('12', substring(newStopTime1, 3, length(newStopTime1)-2)) WHERE newStopTime1 LIKE '24%'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1 = "UPDATE `localanesthesiarecord` SET newStartTime2 = concat('12', substring(newStartTime2, 3, length(newStartTime2)-2)) WHERE newStartTime2 LIKE '24%'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1 = "UPDATE `localanesthesiarecord` SET newStopTime2 = concat('12', substring(newStopTime2, 3, length(newStopTime2)-2)) WHERE newStopTime2 LIKE '24%'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1 = "UPDATE `localanesthesiarecord` SET newStartTime3 = concat('12', substring(newStartTime3, 3, length(newStartTime3)-2)) WHERE newStartTime3 LIKE '24%'";
imw_query($sql1)or $msg_info[] = imw_error();

$sql1 = "UPDATE `localanesthesiarecord` SET newStopTime3 = concat('12', substring(newStopTime3, 3, length(newStopTime3)-2)) WHERE newStopTime3 LIKE '24%'";
imw_query($sql1)or $msg_info[] = imw_error();



if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 114 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 114 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 114</title>
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