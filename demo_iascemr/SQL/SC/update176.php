<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include("../../common/conDb.php");

$msg_info = array();
$sql[] = " ALTER TABLE `operatingroomrecords_supplies` ADD INDEX confirmation_id(confirmation_id);";
$sql[] = " ALTER TABLE `operatingroomrecords_supplies` ADD INDEX suppChkStatus(suppChkStatus);";
$sql[] = " ALTER TABLE `surgery_cost` ADD INDEX confirmation_id(confirmation_id);";
$sql[] = " ALTER TABLE `surgery_cost` ADD INDEX deleted(deleted);";


foreach($sql as $qry){
	imw_query($qry) or $msg_info[] = imw_error();
}

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 176 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 176 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 176</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
imw_close();
}
?> 
</body>
</html>