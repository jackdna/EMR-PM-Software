<?php 
/*update to clean label table*/
$ignoreAuth = true;
include("../../../../config/globals.php");

$sql[]="update `schedule_label_tbl` set `label_type`='Information' where `label_type`=''";
$sql[]="delete from `schedule_label_tbl` where `template_label`=''";
foreach($sql as $q){
	imw_query($q) or $msg_info[] = imw_error();
}


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 13 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 13 completed successfully.</b>`";
	$color = "green";
}
?>
<html>
<head>
<title>Update 13</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>