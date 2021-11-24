<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

$q = "ALTER TABLE chart_master_table ADD INDEX cm_grp (finalize,delete_status,not2show,patient_id,providerId);";

imw_query($q) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 26 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 26 completed successfully. </b>";
	$color = "green";
}

?>
<html>
<head>
<title>Update 26</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>