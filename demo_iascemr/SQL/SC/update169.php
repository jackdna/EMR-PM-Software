<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include("../../common/conDb.php");

$sql[] = "ALTER TABLE `iolink_preophealthquestionnaire` ADD `smokeAdvise` VARCHAR( 5 ) NOT NULL AFTER `smokeHowMuch`  ";
$sql[] = "ALTER TABLE `iolink_preophealthquestionnaire` ADD `alchoholAdvise` VARCHAR( 5 ) NOT NULL AFTER `alchoholHowMuch`  ";

foreach($sql as $qry){
	imw_query($qry)or $msg_info[] = imw_error();
}

include("../../connect_imwemr.php");
$sql1[] = "ALTER TABLE `surgery_center_pre_op_health_ques` ADD `smokeAdvise` VARCHAR( 5 ) NOT NULL AFTER `smokeHowMuch`  ";
$sql1[] = "ALTER TABLE `surgery_center_pre_op_health_ques` ADD `alchoholAdvise` VARCHAR( 5 ) NOT NULL AFTER `alchoholHowMuch`  ";
foreach($sql1 as $qry1){
	imw_query($qry1)or $msg_info[] = imw_error();
}

include("../../common/conDb.php");
$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 169 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 169 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 169</title>
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