<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$sql[] = "ALTER TABLE `healthquestionadmin` ADD `adminQuestionDesc` TEXT NOT NULL ";
$sql[] = "ALTER TABLE `iolink_healthquestionadmin` ADD `adminQuestionDesc` TEXT NOT NULL ";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

//START SQL UPDATE IN IDOC
include_once("../../connect_imwemr.php");
$sqliMedic[]="ALTER TABLE `surgery_center_health_question_admin` ADD `adminQuestionDesc` TEXT NOT NULL";
foreach($sqliMedic as $qryiMedic){
	imw_query($qryiMedic)or $msg_info[] = imw_error();
}


$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 145 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 145 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 145</title>
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